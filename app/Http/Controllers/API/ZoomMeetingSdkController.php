<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ZoomMeetingSdkController extends Controller
{
    /**
     * Returns a short-lived Meeting SDK JWT for initializing the native Zoom Meeting SDK
     * on mobile (Android/iOS). We generate it server-side so the SDK secret never ships
     * in the mobile app.
     */
    public function sdkJwt(Request $request): JsonResponse
    {
        $sdkKey = (string) config('services.zoom_meeting_sdk.key');
        $sdkSecret = (string) config('services.zoom_meeting_sdk.secret');

        if ($sdkKey === '' || $sdkSecret === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Zoom Meeting SDK credentials are missing.',
            ], 400);
        }

        $now = time();
        $exp = $now + 60 * 60; // 1 hour

        // Native/desktop Meeting SDK expects `appKey`. Some docs/tools still reference `sdkKey`.
        // Including both keeps it forward-compatible.
        $payload = [
            'appKey' => $sdkKey,
            'sdkKey' => $sdkKey,
            'iat' => $now,
            'exp' => $exp,
            'tokenExp' => $exp,
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'jwt' => JWT::encode($payload, $sdkSecret, 'HS256'),
                'exp' => $exp,
            ],
        ], 200);
    }
}

