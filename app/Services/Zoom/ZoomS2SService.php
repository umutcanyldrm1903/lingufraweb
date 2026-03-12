<?php

namespace App\Services\Zoom;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ZoomS2SService
{
    public function createMeetingForInstructor(User $instructor, Carbon $startTime, int $durationMinutes, string $topic): array
    {
        $userId = $this->resolveMeetingUserId($instructor);
        $meeting = $this->createMeeting($userId, [
            'topic' => $topic,
            'type' => 2,
            'start_time' => $startTime->toIso8601String(),
            'duration' => max(1, $durationMinutes),
            'timezone' => $this->getTimezone(),
            'settings' => [
                'waiting_room' => true,
                'join_before_host' => false,
                'approval_type' => 0,
                'meeting_authentication' => false,
                'mute_upon_entry' => true,
                'use_pmi' => false,
                'embed_password_in_join_link' => true,
            ],
        ]);

        return $meeting;
    }

    public function createMeeting(string $userId, array $payload): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->timeout(20)
            ->post("https://api.zoom.us/v2/users/{$userId}/meetings", $payload);

        if (!$response->successful()) {
            throw new \RuntimeException('Zoom meeting create failed: '.$response->body());
        }

        return $response->json();
    }

    public function getAccessToken(): string
    {
        $cacheKey = 'zoom_s2s_access_token';
        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $accountId = (string) config('services.zoom_s2s.account_id');
        $clientId = (string) config('services.zoom_s2s.client_id');
        $clientSecret = (string) config('services.zoom_s2s.client_secret');

        if ($accountId === '' || $clientId === '' || $clientSecret === '') {
            throw new \RuntimeException('Zoom S2S credentials are missing.');
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->timeout(20)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Zoom token request failed: '.$response->body());
        }

        $token = (string) $response->json('access_token');
        if ($token === '') {
            throw new \RuntimeException('Zoom token is empty.');
        }

        $expiresIn = (int) $response->json('expires_in', 3600);
        $ttl = max(60, $expiresIn - 60);
        Cache::put($cacheKey, $token, now()->addSeconds($ttl));

        return $token;
    }

    private function resolveMeetingUserId(User $instructor): string
    {
        $useInstructorEmail = filter_var(config('services.zoom_s2s.use_instructor_email', true), FILTER_VALIDATE_BOOL);
        $fallback = (string) config('services.zoom_s2s.default_user_id');

        if ($useInstructorEmail && $instructor->email) {
            return $instructor->email;
        }

        if ($fallback === '') {
            throw new \RuntimeException('Zoom default user id is missing.');
        }

        return $fallback;
    }

    private function getTimezone(): string
    {
        return (string) (config('app.timezone') ?: 'Europe/Istanbul');
    }
}
