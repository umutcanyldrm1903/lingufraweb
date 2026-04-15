<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MobilePushToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        $validated = $request->validate([
            'token' => ['required', 'string', 'max:2048'],
            'platform' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'locale' => ['nullable', 'string', 'max:10'],
            'reminder_window' => ['nullable', 'in:morning,afternoon,evening'],
            'reminders_enabled' => ['nullable', 'boolean'],
        ]);

        $device = MobilePushToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'token' => $validated['token'],
            ],
            [
                'platform' => $validated['platform'] ?? 'unknown',
                'timezone' => $validated['timezone'] ?? 'Europe/Istanbul',
                'locale' => $validated['locale'] ?? app()->getLocale(),
                'reminder_window' => $validated['reminder_window'] ?? 'evening',
                'reminders_enabled' => (bool) ($validated['reminders_enabled'] ?? true),
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => __('Push device registered.'),
            'data' => [
                'id' => $device->id,
            ],
        ]);
    }

    public function unregister(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        $validated = $request->validate([
            'token' => ['required', 'string', 'max:2048'],
        ]);

        MobilePushToken::query()
            ->where('user_id', $user->id)
            ->where('token', $validated['token'])
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Push device removed.'),
        ]);
    }
}
