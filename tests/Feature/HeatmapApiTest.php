<?php

namespace Tests\Feature;

use App\Models\ActivityEvent;
use App\Models\TrackingSession;
use Tests\TestCase;

class HeatmapApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');
        $this->artisan('migrate');
    }

    public function test_heatmap_returns_points(): void
    {
        $session = TrackingSession::create([
            'session_id' => 'hm-001',
            'operator_name' => 'Test Operator',
            'status' => 'verified',
            'start_time' => now()->subHours(2),
            'end_time' => now()->subHour(),
            'total_distance' => 500,
            'total_track_points' => 10,
        ]);

        ActivityEvent::create([
            'id' => 'evt-hm-1',
            'session_id' => 'hm-001',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'timestamp' => now()->subHours(2),
            'status' => 'verified',
        ]);

        $response = $this->getJson('/api/analytics/heatmap?days=7');
        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('count', 1)
            ->assertJsonStructure(['data' => [['lat', 'lng', 'weight']]]);
    }

    public function test_heatmap_filters_by_category(): void
    {
        TrackingSession::create([
            'session_id' => 'hm-002',
            'operator_name' => 'Op',
            'status' => 'verified',
            'start_time' => now()->subHours(3),
            'end_time' => now()->subHour(),
            'total_distance' => 100,
            'total_track_points' => 5,
        ]);

        ActivityEvent::create([
            'id' => 'evt-hm-2a',
            'session_id' => 'hm-002',
            'latitude' => -6.3,
            'longitude' => 106.9,
            'timestamp' => now()->subHours(2),
            'status' => 'verified',
            'operator_category' => 'Pohon Tumbang',
        ]);

        ActivityEvent::create([
            'id' => 'evt-hm-2b',
            'session_id' => 'hm-002',
            'latitude' => -6.4,
            'longitude' => 107.0,
            'timestamp' => now()->subHours(2),
            'status' => 'verified',
            'operator_category' => 'Banjir',
        ]);

        $response = $this->getJson('/api/analytics/heatmap?days=7&category=Banjir');
        $response->assertOk()
            ->assertJsonPath('count', 1);
    }
}
