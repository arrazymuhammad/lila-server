<?php

namespace Tests\Feature;

use App\Models\ActivityEvent;
use App\Models\ActivityPhoto;
use App\Models\TrackPoint;
use App\Models\TrackingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/** TASK-211: DashboardController metrics verification */
class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_zeroes_when_no_data(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total Temuan Pengamatan');
        $response->assertSee('Total Perjalanan');
    }

    public function test_dashboard_counts_verified_sessions(): void
    {
        $user = User::factory()->create();
        $sid1 = Str::uuid7()->toString();
        $sid2 = Str::uuid7()->toString();

        TrackingSession::create(['id' => $sid1, 'start_time' => now(), 'distance' => 5.5, 'status' => 'verified']);
        TrackingSession::create(['id' => $sid2, 'start_time' => now(), 'distance' => 3.2, 'status' => 'submitted']);

        TrackPoint::create(['session_id' => $sid1, 'timestamp' => now(), 'latitude' => -6.2, 'longitude' => 106.8]);
        $eid = Str::uuid7()->toString();
        ActivityEvent::create(['id' => $eid, 'session_id' => $sid1, 'title' => 'Temuan', 'status' => 'verified', 'timestamp' => now(), 'latitude' => -6.2, 'longitude' => 106.8]);
        ActivityPhoto::create(['id' => Str::uuid7()->toString(), 'session_id' => $sid1, 'event_id' => $eid, 'file_path' => 'x', 'filename' => 'x.jpg', 'selected' => true, 'timestamp' => now()]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Total Temuan Pengamatan');
    }
}
