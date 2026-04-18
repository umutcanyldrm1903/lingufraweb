<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileAnalyticsEvent;
use App\Models\MobileStoreMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrowthDashboardController extends Controller
{
    public function index(Request $request)
    {
        $days = max(1, min(60, (int) $request->query('days', 14)));
        $since = now()->subDays($days);

        $eventCounts = MobileAnalyticsEvent::query()
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

        $segmentCounts = MobileAnalyticsEvent::query()
            ->where('event_time', '>=', $since)
            ->whereNotNull('segment')
            ->select('segment', DB::raw('count(*) as total'))
            ->groupBy('segment')
            ->pluck('total', 'segment');

        $experimentCounts = MobileAnalyticsEvent::query()
            ->where('event_time', '>=', $since)
            ->whereNotNull('experiment')
            ->select('experiment', DB::raw('count(*) as total'))
            ->groupBy('experiment')
            ->pluck('total', 'experiment');

        $storeMetrics = MobileStoreMetric::query()
            ->where('metric_date', '>=', now()->subDays($days)->toDateString())
            ->orderByDesc('metric_date')
            ->limit(30)
            ->get();

        $totals = [
            'opened' => (int) ($eventCounts['speaking_opened'] ?? 0),
            'started' => (int) ($eventCounts['record_started'] ?? 0),
            'completed' => (int) ($eventCounts['record_completed'] ?? 0),
            'trialTapped' => (int) ($eventCounts['trial_cta_tapped'] ?? 0),
            'trialRequested' => (int) ($eventCounts['trial_requested'] ?? 0),
            'bookingStarted' => (int) ($eventCounts['booking_started'] ?? 0),
        ];

        return view('admin.growth.dashboard', [
            'days' => $days,
            'totals' => $totals,
            'segmentCounts' => $segmentCounts,
            'experimentCounts' => $experimentCounts,
            'storeMetrics' => $storeMetrics,
        ]);
    }

    public function storeMetric(Request $request)
    {
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

        return redirect()->route('admin.growth.dashboard')->with([
            'messege' => __('Store metric saved.'),
            'alert-type' => 'success',
        ]);
    }
}
