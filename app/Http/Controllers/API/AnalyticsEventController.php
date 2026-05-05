<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MobileAnalyticsEvent;
use App\Models\MobileStoreMetric;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsEventController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $configuredKey = (string) config('app.mobile_analytics_key', '');
        if ($configuredKey !== '') {
            $incomingKey = (string) $request->header('X-Analytics-Key', '');
            if (!hash_equals($configuredKey, $incomingKey)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized analytics key.',
                ], 401);
            }
        }

        $validated = $request->validate([
            'source' => ['nullable', 'string', 'max:32'],
            'events' => ['required', 'array', 'min:1', 'max:200'],
            'events.*.name' => ['required', 'string', 'max:120'],
            'events.*.timestamp_iso' => ['nullable', 'string', 'max:64'],
            'events.*.properties' => ['nullable', 'array'],
        ]);

        $source = strtolower((string) ($validated['source'] ?? 'mobile'));
        if (!in_array($source, ['mobile', 'web', 'api'], true)) {
            $source = 'mobile';
        }
        $events = $validated['events'];
        $userId = auth()->id();
        $now = now();
        $insert = [];

        foreach ($events as $event) {
            $properties = is_array($event['properties'] ?? null)
                ? $this->sanitizeProperties($event['properties'])
                : [];
            $eventTime = $now;
            try {
                $eventTime = CarbonImmutable::parse((string) ($event['timestamp_iso'] ?? ''));
            } catch (\Throwable) {
                $eventTime = $now;
            }
            $segment = $this->safeString($properties['segment'] ?? null, 40);
            $experiment = $this->safeString($properties['experiment'] ?? null, 60);

            $insert[] = [
                'user_id' => $userId ? (int) $userId : null,
                'source' => $source,
                'name' => (string) ($event['name'] ?? ''),
                'event_time' => $eventTime->toDateTimeString(),
                'segment' => $segment,
                'experiment' => $experiment,
                'ip' => $request->ip(),
                'properties' => json_encode($properties, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ];
        }

        MobileAnalyticsEvent::insert($insert);
        $this->trimRetention();

        return response()->json([
            'status' => 'success',
            'data' => [
                'accepted' => count($events),
            ],
        ]);
    }

    public function exportMine(Request $request): JsonResponse
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 401);
        }

        $rows = MobileAnalyticsEvent::query()
            ->where('user_id', $userId)
            ->orderByDesc('event_time')
            ->limit(5000)
            ->get([
                'name',
                'event_time',
                'segment',
                'experiment',
                'properties',
            ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'events' => $rows,
            ],
        ]);
    }

    public function deleteMine(Request $request): JsonResponse
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 401);
        }

        $deleted = MobileAnalyticsEvent::query()->where('user_id', $userId)->delete();

        return response()->json([
            'status' => 'success',
            'data' => [
                'deleted' => $deleted,
            ],
        ]);
    }

    public function ingestStoreMetrics(Request $request): JsonResponse
    {
        $configuredKey = (string) config('app.mobile_analytics_key', '');
        if ($configuredKey !== '') {
            $incomingKey = (string) $request->header('X-Analytics-Key', '');
            if (!hash_equals($configuredKey, $incomingKey)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized analytics key.',
                ], 401);
            }
        }

        $validated = $request->validate([
            'metric_date' => ['required', 'date'],
            'store_page_views' => ['required', 'integer', 'min:0'],
            'installs' => ['required', 'integer', 'min:0'],
            'trial_starts' => ['required', 'integer', 'min:0'],
            'trial_conversions' => ['required', 'integer', 'min:0'],
            'channel' => ['nullable', 'string', 'max:32'],
        ]);

        MobileStoreMetric::updateOrCreate(
            ['metric_date' => $validated['metric_date']],
            [
                'store_page_views' => $validated['store_page_views'],
                'installs' => $validated['installs'],
                'trial_starts' => $validated['trial_starts'],
                'trial_conversions' => $validated['trial_conversions'],
                'channel' => $validated['channel'] ?? 'organic',
            ]
        );

        return response()->json([
            'status' => 'success',
            'data' => ['saved' => true],
        ]);
    }

    public function speakingCoachFunnel(Request $request): JsonResponse
    {
        $days = max(1, min(60, (int) $request->query('days', 7)));
        $since = now()->subDays($days);

        $counts = MobileAnalyticsEvent::query()
            ->where('event_time', '>=', $since)
            ->whereIn('name', [
                'speaking_opened',
                'record_started',
                'record_completed',
                'trial_cta_tapped',
                'trial_requested',
                'booking_started',
            ])
            ->select('name', DB::raw('count(*) as total'))
            ->groupBy('name')
            ->pluck('total', 'name');

        $opened = (int) ($counts['speaking_opened'] ?? 0);
        $started = (int) ($counts['record_started'] ?? 0);
        $completed = (int) ($counts['record_completed'] ?? 0);
        $trialTapped = (int) ($counts['trial_cta_tapped'] ?? 0);
        $trialRequested = (int) ($counts['trial_requested'] ?? 0);
        $bookingStarted = (int) ($counts['booking_started'] ?? 0);

        return response()->json([
            'status' => 'success',
            'data' => [
                'days' => $days,
                'counts' => [
                    'speaking_opened' => $opened,
                    'record_started' => $started,
                    'record_completed' => $completed,
                    'trial_cta_tapped' => $trialTapped,
                    'trial_requested' => $trialRequested,
                    'booking_started' => $bookingStarted,
                ],
                'rates' => [
                    'start_rate' => $this->ratio($started, $opened),
                    'completion_rate' => $this->ratio($completed, $started),
                    'trial_tap_rate' => $this->ratio($trialTapped, $completed),
                    'trial_request_rate' => $this->ratio($trialRequested, $trialTapped),
                    'booking_rate' => $this->ratio($bookingStarted, $trialRequested),
                ],
            ],
        ]);
    }

    private function sanitizeProperties(array $raw): array
    {
        $clean = [];
        foreach ($raw as $key => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $safeKey = substr((string) $key, 0, 60);
            if ($safeKey === '') {
                continue;
            }
            $clean[$safeKey] = substr((string) $value, 0, 240);
        }
        return $clean;
    }

    private function safeString(mixed $value, int $limit): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $clean = trim((string) $value);
        if ($clean === '') {
            return null;
        }

        return substr($clean, 0, $limit);
    }

    private function ratio(int $numerator, int $denominator): float
    {
        if ($denominator <= 0) {
            return 0.0;
        }
        return round($numerator / $denominator, 4);
    }

    private function trimRetention(): void
    {
        if (random_int(1, 20) !== 1) {
            return;
        }

        MobileAnalyticsEvent::query()
            ->where('created_at', '<', now()->subDays(120))
            ->delete();
    }
}
