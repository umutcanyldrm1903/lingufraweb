<?php

namespace App\Services\Zoom;

use App\Models\User;
use App\Models\ZoomCredential;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ZoomOAuthService
{
    public function buildAuthorizationUrl(User $instructor): string
    {
        $clientId = (string) config('services.zoom_oauth.client_id');
        $redirectUri = (string) config('services.zoom_oauth.redirect');

        if ($clientId === '' || $redirectUri === '') {
            throw new \RuntimeException('Zoom OAuth configuration is missing.');
        }

        $state = Str::random(40);
        Session::put('zoom_oauth_state', $state);
        Session::put('zoom_oauth_user_id', $instructor->id);

        return 'https://zoom.us/oauth/authorize?response_type=code'
            .'&client_id='.urlencode($clientId)
            .'&redirect_uri='.urlencode($redirectUri)
            .'&state='.urlencode($state);
    }

    public function handleCallback(User $instructor, string $code, ?string $state = null): ZoomCredential
    {
        $savedState = Session::pull('zoom_oauth_state');
        $savedUserId = (int) Session::pull('zoom_oauth_user_id');

        if (!$savedState || !$state || $savedState !== $state || $savedUserId !== (int) $instructor->id) {
            throw new \RuntimeException('Zoom OAuth state mismatch.');
        }

        $tokenData = $this->exchangeCodeForToken($code);

        $zoomUser = $this->fetchZoomUser($tokenData['access_token'] ?? '');

        return $this->storeCredential($instructor, $tokenData, $zoomUser);
    }

    public function createMeetingForInstructor(User $instructor, Carbon $startTime, int $durationMinutes, string $topic): array
    {
        $credential = $this->getCredentialOrFail($instructor);
        $accessToken = $this->getValidAccessToken($credential);

        $userId = $credential->zoom_user_id ?: 'me';

        $response = Http::withToken($accessToken)
            ->timeout(20)
            ->post("https://api.zoom.us/v2/users/{$userId}/meetings", [
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

        if (!$response->successful()) {
            throw new \RuntimeException('Zoom meeting create failed: '.$response->body());
        }

        return $response->json();
    }

    /**
     * Creates (once) and reuses a single recurring "room" meeting for an instructor.
     *
     * This avoids creating a new Zoom meeting per reservation. All private lessons
     * can reuse the same meeting_id/password/join_url.
     */
    public function getOrCreateDefaultRecurringMeeting(User $instructor, string $topic = 'Lesson Room'): array
    {
        // Allow "manual" setup: instructor can set a fixed join URL without connecting OAuth.
        // In that case, we simply reuse what is stored.
        $credential = ZoomCredential::where('instructor_id', $instructor->id)->first();
        if ($credential && $credential->default_meeting_id) {
            $joinUrl = trim((string) ($credential->default_join_url ?? ''));

            return [
                'id' => (string) $credential->default_meeting_id,
                'join_url' => $joinUrl !== '' ? $joinUrl : $this->buildZoomJoinUrl((string) $credential->default_meeting_id),
                'password' => $credential->default_meeting_password ? (string) $credential->default_meeting_password : null,
            ];
        }

        // Otherwise, fallback to OAuth flow to create the recurring meeting once.
        $credential = $this->getCredentialOrFail($instructor);

        $accessToken = $this->getValidAccessToken($credential);
        $userId = $credential->zoom_user_id ?: 'me';

        $response = Http::withToken($accessToken)
            ->timeout(20)
            ->post("https://api.zoom.us/v2/users/{$userId}/meetings", [
                'topic' => $topic !== '' ? $topic : 'Lesson Room',
                // Recurring meeting with no fixed time (single reusable join link).
                'type' => 3,
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

        if (!$response->successful()) {
            throw new \RuntimeException('Zoom meeting create failed: '.$response->body());
        }

        $meeting = (array) $response->json();
        $meetingId = (string) ($meeting['id'] ?? '');
        if ($meetingId === '') {
            throw new \RuntimeException('Zoom meeting id missing.');
        }

        $credential->update([
            'default_meeting_id' => $meetingId,
            'default_meeting_password' => $meeting['password'] ?? null,
            'default_join_url' => $meeting['join_url'] ?? null,
            'default_meeting_created_at' => now(),
        ]);

        return $meeting;
    }

    public function disconnect(User $instructor): void
    {
        $credential = ZoomCredential::where('instructor_id', $instructor->id)->first();
        if (!$credential) {
            return;
        }

        $credential->update([
            'access_token' => null,
            'refresh_token' => null,
            'token_expires_at' => null,
            'zoom_user_id' => null,
            'zoom_email' => null,
            'scope' => null,
            'default_meeting_id' => null,
            'default_meeting_password' => null,
            'default_join_url' => null,
            'default_meeting_created_at' => null,
        ]);
    }

    public function isConnected(User $instructor): bool
    {
        $credential = ZoomCredential::where('instructor_id', $instructor->id)->first();
        return $credential && $credential->access_token && $credential->refresh_token;
    }

    private function exchangeCodeForToken(string $code): array
    {
        $clientId = (string) config('services.zoom_oauth.client_id');
        $clientSecret = (string) config('services.zoom_oauth.client_secret');
        $redirectUri = (string) config('services.zoom_oauth.redirect');

        if ($clientId === '' || $clientSecret === '' || $redirectUri === '') {
            throw new \RuntimeException('Zoom OAuth configuration is missing.');
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->timeout(20)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Zoom OAuth token request failed: '.$response->body());
        }

        return $response->json();
    }

    private function refreshAccessToken(string $refreshToken): array
    {
        $clientId = (string) config('services.zoom_oauth.client_id');
        $clientSecret = (string) config('services.zoom_oauth.client_secret');

        if ($clientId === '' || $clientSecret === '') {
            throw new \RuntimeException('Zoom OAuth configuration is missing.');
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->timeout(20)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Zoom OAuth refresh request failed: '.$response->body());
        }

        return $response->json();
    }

    private function fetchZoomUser(string $accessToken): array
    {
        if ($accessToken === '') {
            return [];
        }

        $response = Http::withToken($accessToken)
            ->timeout(20)
            ->get('https://api.zoom.us/v2/users/me');

        if (!$response->successful()) {
            return [];
        }

        return (array) $response->json();
    }

    private function storeCredential(User $instructor, array $tokenData, array $zoomUser = []): ZoomCredential
    {
        $expiresIn = (int) ($tokenData['expires_in'] ?? 0);
        $expiresAt = $expiresIn > 0 ? now()->addSeconds(max(60, $expiresIn - 60)) : null;

        $existing = ZoomCredential::where('instructor_id', $instructor->id)->first();

        return ZoomCredential::updateOrCreate(
            ['instructor_id' => $instructor->id],
            [
                'client_id' => $existing?->client_id ?? '',
                'client_secret' => $existing?->client_secret ?? '',
                'access_token' => $tokenData['access_token'] ?? null,
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'token_expires_at' => $expiresAt,
                'zoom_user_id' => $zoomUser['id'] ?? null,
                'zoom_email' => $zoomUser['email'] ?? null,
                'scope' => $tokenData['scope'] ?? null,
            ]
        );
    }

    private function getValidAccessToken(ZoomCredential $credential): string
    {
        if (!$credential->access_token || !$credential->refresh_token) {
            throw new \RuntimeException('Zoom account not connected.');
        }

        if ($credential->token_expires_at && now()->lt($credential->token_expires_at)) {
            return $credential->access_token;
        }

        $tokenData = $this->refreshAccessToken($credential->refresh_token);
        $expiresIn = (int) ($tokenData['expires_in'] ?? 0);
        $expiresAt = $expiresIn > 0 ? now()->addSeconds(max(60, $expiresIn - 60)) : null;

        $credential->update([
            'access_token' => $tokenData['access_token'] ?? $credential->access_token,
            'refresh_token' => $tokenData['refresh_token'] ?? $credential->refresh_token,
            'token_expires_at' => $expiresAt,
            'scope' => $tokenData['scope'] ?? $credential->scope,
        ]);

        return (string) $credential->access_token;
    }

    private function getCredentialOrFail(User $instructor): ZoomCredential
    {
        $credential = ZoomCredential::where('instructor_id', $instructor->id)->first();
        if (!$credential || !$credential->refresh_token) {
            throw new \RuntimeException('Zoom account not connected.');
        }

        return $credential;
    }

    private function getTimezone(): string
    {
        return (string) (config('app.timezone') ?: 'Europe/Istanbul');
    }

    private function buildZoomJoinUrl(string $meetingId): string
    {
        $meetingId = preg_replace('/[^0-9]/', '', $meetingId);

        if ($meetingId === '') {
            return '';
        }

        // We can't reliably generate Zoom's `pwd` token without the Zoom API.
        // This URL will prompt for passcode if the meeting is protected.
        return 'https://zoom.us/j/'.$meetingId;
    }
}
