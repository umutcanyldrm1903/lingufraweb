<?php

namespace Tests\Feature;

use App\Models\MobileAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsEventsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_events_are_saved_to_database(): void
    {
        $response = $this->postJson('/api/analytics/events', [
            'source' => 'mobile',
            'events' => [
                [
                    'name' => 'speaking_opened',
                    'timestamp_iso' => now()->toIso8601String(),
                    'properties' => [
                        'segment' => 'warming_up',
                        'experiment' => 'trial_cta_v1_b',
                    ],
                ],
            ],
        ]);

        $response->assertOk()->assertJsonPath('data.accepted', 1);
        $this->assertDatabaseCount('mobile_analytics_events', 1);
        $this->assertSame('speaking_opened', MobileAnalyticsEvent::query()->value('name'));
    }
}
