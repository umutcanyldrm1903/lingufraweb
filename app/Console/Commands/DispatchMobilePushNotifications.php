<?php

namespace App\Console\Commands;

use App\Models\MobilePushToken;
use App\Models\StudentLiveLesson;
use App\Services\Push\FcmPushService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class DispatchMobilePushNotifications extends Command
{
    protected $signature = 'push:dispatch-mobile';

    protected $description = 'Dispatch mobile push notifications for daily routines and lesson reminders.';

    public function __construct(
        private readonly FcmPushService $fcmPushService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (!$this->fcmPushService->isConfigured()) {
            $this->warn('FCM is not configured. Skipping push dispatch.');
            return self::SUCCESS;
        }

        $this->dispatchDailyReminders();
        $this->dispatchUpcomingLessonReminders();

        return self::SUCCESS;
    }

    private function dispatchDailyReminders(): void
    {
        MobilePushToken::query()
            ->where('reminders_enabled', true)
            ->whereNotNull('token')
            ->with('user:id,name')
            ->chunkById(100, function (Collection $devices) {
                foreach ($devices as $device) {
                    $now = Carbon::now($device->timezone ?: 'Europe/Istanbul');
                    $targetHour = match ($device->reminder_window) {
                        'morning' => 9,
                        'afternoon' => 14,
                        default => 19,
                    };

                    if ((int) $now->format('G') !== $targetHour) {
                        continue;
                    }

                    if ($device->last_daily_sent_on && $device->last_daily_sent_on->isSameDay($now)) {
                        continue;
                    }

                    $title = $device->locale === 'tr'
                        ? 'Gunluk speaking gorevin hazir'
                        : 'Your daily speaking task is ready';
                    $body = $device->locale === 'tr'
                        ? 'Mini paketini ac, streaki koru ve uygun hocayi kacirma.'
                        : 'Open your mini pack, keep your streak alive, and do not miss the right tutor.';

                    $sent = $this->fcmPushService->sendToToken($device, $title, $body, [
                        'type' => 'daily_routine',
                    ]);

                    if ($sent) {
                        $device->forceFill([
                            'last_daily_sent_on' => $now->toDateString(),
                            'last_seen_at' => now(),
                        ])->save();
                    }
                }
            });
    }

    private function dispatchUpcomingLessonReminders(): void
    {
        $minutes = max(1, (int) config('fcm.lesson_reminder_minutes', 30));
        $windowStart = now()->addMinutes($minutes);
        $windowEnd = now()->addMinutes($minutes + 1);

        $lessons = StudentLiveLesson::query()
            ->with([
                'student:id,name',
                'instructor:id,name',
                'student.mobilePushTokens',
                'instructor.mobilePushTokens',
            ])
            ->where('status', 'scheduled')
            ->whereNull('push_reminder_sent_at')
            ->whereBetween('start_time', [$windowStart, $windowEnd])
            ->get();

        foreach ($lessons as $lesson) {
            foreach ([
                ['user' => $lesson->student, 'role' => 'student'],
                ['user' => $lesson->instructor, 'role' => 'instructor'],
            ] as $target) {
                $user = $target['user'];
                if (!$user) {
                    continue;
                }

                foreach ($user->mobilePushTokens as $device) {
                    $title = $device->locale === 'tr'
                        ? 'Yaklasan ders hatirlatmasi'
                        : 'Upcoming lesson reminder';
                    $body = $device->locale === 'tr'
                        ? 'Dersin ' . formattedDateTime($lesson->start_time) . ' saatinde basliyor.'
                        : 'Your lesson starts at ' . formattedDateTime($lesson->start_time) . '.';

                    $this->fcmPushService->sendToToken($device, $title, $body, [
                        'type' => 'lesson_reminder',
                        'lesson_id' => $lesson->id,
                        'role' => $target['role'],
                    ]);
                }
            }

            $lesson->forceFill([
                'push_reminder_sent_at' => now(),
            ])->save();
        }
    }
}
