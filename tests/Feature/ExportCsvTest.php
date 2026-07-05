<?php

namespace Tests\Feature;

use App\Models\TrackingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/** TASK-212: ExportController CSV integrity */
class ExportCsvTest extends TestCase
{
    use RefreshDatabase;

    public function test_sessions_csv_includes_verified_rows(): void
    {
        $user = User::factory()->create();
        $sid = Str::uuid7()->toString();
        TrackingSession::create(['id' => $sid, 'title' => 'Export Test', 'start_time' => now(), 'status' => 'verified']);
        TrackingSession::create(['id' => Str::uuid7()->toString(), 'start_time' => now(), 'status' => 'submitted']);

        $response = $this->actingAs($user)->get('/export/sessions');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('Export Test', $content);
        $lines = array_filter(explode("\n", $content));
        $this->assertGreaterThanOrEqual(2, count($lines));
    }
}
