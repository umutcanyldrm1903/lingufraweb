<?php

namespace App\Services\Push;

use App\Models\MobilePushToken;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FcmPushService
{
    public function __construct(
        private readonly Client $http = new Client(['timeout' => 15]),
    ) {
    }

    public function isConfigured(): bool
    {
        return (bool) config('fcm.enabled')
            && filled(config('fcm.project_id'))
            && filled(config('fcm.service_account_email'))
            && filled(config('fcm.service_account_private_key'));
    }

    public function sendToToken(
        MobilePushToken $device,
        string $title,
        string $body,
        array $data = [],
    ): bool {
        if (!$this->isConfigured() || blank($device->token)) {
            return false;
        }

        try {
            $accessToken = $this->getAccessToken();
            if ($accessToken === null) {
                return false;
            }

            $projectId = (string) config('fcm.project_id');
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $response = $this->http->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'message' => [
                        'token' => $device->token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => collect($data)
                            ->map(fn ($value) => (string) $value)
                            ->all(),
                    ],
                ],
            ]);

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
            Log::warning('FCM push send failed', [
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'error' => $message,
            ]);

            if (str_contains($message, 'UNREGISTERED') || str_contains($message, 'registration-token-not-registered')) {
                $device->delete();
            }

            return false;
        }
    }

    private function getAccessToken(): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $cacheKey = (string) config('fcm.token_cache_key', 'fcm_access_token');
        $ttlMinutes = (int) config('fcm.token_cache_ttl_minutes', 55);

        return Cache::remember($cacheKey, now()->addMinutes($ttlMinutes), function () {
            $issuedAt = Carbon::now()->timestamp;
            $expiresAt = Carbon::now()->addHour()->timestamp;

            $privateKey = str_replace('\n', "\n", (string) config('fcm.service_account_private_key'));

            $jwt = JWT::encode([
                'iss' => config('fcm.service_account_email'),
                'scope' => config('fcm.oauth_scope'),
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $issuedAt,
                'exp' => $expiresAt,
            ], $privateKey, 'RS256');

            $response = $this->http->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ],
            ]);

            $payload = json_decode((string) $response->getBody(), true);

            return is_array($payload) ? ($payload['access_token'] ?? null) : null;
        });
    }
}
