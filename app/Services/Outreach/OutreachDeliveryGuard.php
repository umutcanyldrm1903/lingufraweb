<?php

namespace App\Services\Outreach;

use App\Models\OutreachMessage;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class OutreachDeliveryGuard
{
    public function evaluate(OutreachMessage $message): array
    {
        $message->loadMissing('campaign', 'lead');
        $campaign = $message->campaign;

        $nowUtc = now()->utc();
        $blockedUntil = [];
        $reasons = [];

        $hoursBlock = $this->hoursBlock($campaign->timezone, (int) $campaign->send_start_hour, (int) $campaign->send_end_hour);

        if ($hoursBlock) {
            $blockedUntil[] = $hoursBlock;
            $reasons[] = 'outside send window';
        }

        $lastHourSent = OutreachMessage::where('campaign_id', $campaign->id)
            ->whereNotNull('sent_at')
            ->where('sent_at', '>=', $nowUtc->copy()->subHour())
            ->orderBy('sent_at')
            ->get(['sent_at']);

        if ($campaign->hourly_send_limit > 0 && $lastHourSent->count() >= $campaign->hourly_send_limit) {
            $blockedUntil[] = Carbon::parse($lastHourSent->first()->sent_at)->utc()->addHour();
            $reasons[] = 'hourly limit reached';
        }

        $lastDaySent = OutreachMessage::where('campaign_id', $campaign->id)
            ->whereNotNull('sent_at')
            ->where('sent_at', '>=', $nowUtc->copy()->subDay())
            ->orderBy('sent_at')
            ->get(['sent_at']);

        if ($campaign->daily_send_limit > 0 && $lastDaySent->count() >= $campaign->daily_send_limit) {
            $blockedUntil[] = Carbon::parse($lastDaySent->first()->sent_at)->utc()->addDay();
            $reasons[] = 'daily limit reached';
        }

        $latestSent = OutreachMessage::where('campaign_id', $campaign->id)
            ->whereNotNull('sent_at')
            ->latest('sent_at')
            ->first(['sent_at']);

        if ($latestSent && $campaign->min_delay_seconds > 0) {
            $nextAllowed = Carbon::parse($latestSent->sent_at)->utc()->addSeconds((int) $campaign->min_delay_seconds);

            if ($nextAllowed->greaterThan($nowUtc)) {
                $blockedUntil[] = $nextAllowed;
                $reasons[] = 'minimum delay active';
            }
        }

        if ($blockedUntil === []) {
            return [
                'allowed' => true,
                'reason' => null,
                'next_attempt_at' => null,
            ];
        }

        return [
            'allowed' => false,
            'reason' => implode(', ', $reasons),
            'next_attempt_at' => $this->maxCarbon($blockedUntil),
        ];
    }

    protected function hoursBlock(string $timezone, int $startHour, int $endHour): ?CarbonInterface
    {
        try {
            $localNow = now()->setTimezone($timezone ?: 'UTC');
        } catch (\Throwable $throwable) {
            $localNow = now()->setTimezone('UTC');
        }

        $start = $localNow->copy()->startOfDay()->addHours($startHour);
        $end = $localNow->copy()->startOfDay()->addHours($endHour);

        if ($endHour <= $startHour) {
            $end = $end->addDay();
        }

        if ($localNow->lessThan($start)) {
            return $start->copy()->utc();
        }

        if ($localNow->greaterThanOrEqualTo($end)) {
            return $start->copy()->addDay()->utc();
        }

        return null;
    }

    protected function maxCarbon(array $dates): ?CarbonInterface
    {
        return collect($dates)->reduce(function (?CarbonInterface $carry, CarbonInterface $date) {
            if (! $carry) {
                return $date;
            }

            return $date->greaterThan($carry) ? $date : $carry;
        });
    }
}
