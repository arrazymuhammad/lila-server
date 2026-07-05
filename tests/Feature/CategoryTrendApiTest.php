<?php

namespace Tests\Feature;

use App\Models\ActivityEvent;
use App\Models\TrackingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/** TASK-203: CategoryTrendController API test */
class CategoryTrendApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_category_trend_data(): void
    {
        $sid = Str::uuid7()->toString();
        TrackingSession::create(['id' => $sid, 'start_time' => now(), 'status' => 'verified']);
        ActivityEvent::create([
            'id' => Str::uuid7()->toString(),
            'session_id' => $sid,
            'title' => 'E1',
            'status' => 'verified',
            'operator_category' => 'Infrastruktur',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'timestamp' => now(),
        ]);

        $response = $this->getJson('/api/analytics/category-trends');
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonFragment([
                'category' => 'Infrastruktur',
            ]);
    }
}
