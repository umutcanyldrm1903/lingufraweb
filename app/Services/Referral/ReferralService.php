<?php

namespace App\Services\Referral;

use App\Models\User;
use App\Models\UserOnboarding;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Order\app\Models\Order;

class ReferralService
{
    public function ensureReferralCode(User $user, int $length = 8): string
    {
        $onboarding = UserOnboarding::firstOrCreate(['user_id' => $user->id]);
        $existing = trim((string) ($onboarding->referral_code ?? ''));
        if ($existing !== '') {
            return $existing;
        }

        $code = '';
        $tries = 0;
        do {
            $tries++;
            $candidate = Str::random($length);
            $exists = UserOnboarding::query()->where('referral_code', $candidate)->exists();
            if (!$exists) {
                $code = $candidate;
                break;
            }
        } while ($tries < 25);

        if ($code === '') {
            $code = str_replace('-', '', (string) Str::uuid());
            $code = substr($code, 0, $length);
        }

        $onboarding->referral_code = $code;
        $onboarding->save();

        return $code;
    }

    public function attachReferrerFromCode(User $newUser, ?string $referralCode): ?int
    {
        $code = trim((string) $referralCode);
        if ($code === '') {
            return null;
        }

        if (!Schema::hasTable('user_onboardings') || !Schema::hasColumn('user_onboardings', 'referred_by_user_id')) {
            return null;
        }

        $referrerId = (int) UserOnboarding::query()
            ->where('referral_code', $code)
            ->where('user_id', '!=', $newUser->id)
            ->value('user_id');

        if ($referrerId <= 0 || $referrerId === (int) $newUser->id) {
            return null;
        }

        UserOnboarding::updateOrCreate(
            ['user_id' => $newUser->id],
            ['referred_by_user_id' => $referrerId]
        );

        return $referrerId;
    }

    public function rewardReferrerForPaidPlanOrder(Order $order): void
    {
        if (($order->order_type ?? null) !== 'student_plan') {
            return;
        }

        if (!Schema::hasTable('user_onboardings') || !Schema::hasColumn('user_onboardings', 'referred_by_user_id')) {
            return;
        }

        if (!Schema::hasTable('referral_rewards')) {
            return;
        }

        if (!Schema::hasTable('user_plans')) {
            return;
        }

        $buyerId = (int) ($order->buyer_id ?? 0);
        if ($buyerId <= 0) {
            return;
        }

        $referrerId = (int) DB::table('user_onboardings')
            ->where('user_id', $buyerId)
            ->value('referred_by_user_id');

        if ($referrerId <= 0 || $referrerId === $buyerId) {
            return;
        }

        $details = $order->orderDetails;
        $lessonsTotal = (int) ($details?->lessons_total ?? 0);
        $rewardLessons = $this->calculateRewardLessons($lessonsTotal);

        if ($rewardLessons <= 0) {
            return;
        }

        DB::transaction(function () use ($order, $buyerId, $referrerId, $rewardLessons) {
            $alreadyRewarded = DB::table('referral_rewards')
                ->where('referred_user_id', $buyerId)
                ->lockForUpdate()
                ->exists();

            if ($alreadyRewarded) {
                return;
            }

            try {
                DB::table('referral_rewards')->insert([
                    'referrer_id' => $referrerId,
                    'referred_user_id' => $buyerId,
                    'order_id' => $order->id,
                    'reward_lessons' => $rewardLessons,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Likely a duplicate reward; skip silently.
                return;
            }

            $plan = DB::table('user_plans')->where('user_id', $referrerId)->lockForUpdate()->first();
            if ($plan) {
                DB::table('user_plans')
                    ->where('user_id', $referrerId)
                    ->update([
                        'lessons_total' => (int) ($plan->lessons_total ?? 0) + $rewardLessons,
                        'lessons_remaining' => (int) ($plan->lessons_remaining ?? 0) + $rewardLessons,
                        'updated_at' => now(),
                    ]);
                return;
            }

            // Create a credit wallet row without setting a paid plan title, so the UI can still show "Plan Yok".
            DB::table('user_plans')->insert([
                'user_id' => $referrerId,
                'plan_key' => null,
                'plan_title' => null,
                'lessons_total' => $rewardLessons,
                'lessons_remaining' => $rewardLessons,
                'cancel_total' => 0,
                'cancel_remaining' => 0,
                'starts_at' => null,
                'ends_at' => null,
                'last_order_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    private function calculateRewardLessons(int $lessonsTotal): int
    {
        if ($lessonsTotal >= 96) {
            return 3;
        }
        if ($lessonsTotal >= 48) {
            return 2;
        }
        if ($lessonsTotal > 0) {
            return 1;
        }
        return 0;
    }
}
