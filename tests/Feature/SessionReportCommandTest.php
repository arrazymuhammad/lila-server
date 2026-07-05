<?php

namespace Tests\Feature;

use App\Console\Commands\SessionReportCommand;
use App\Models\TrackingSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/** TASK-215: SessionReportCommand stdout test */
class SessionReportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_outputs_report_summary(): void
    {
        $sid = Str::uuid7()->toString();
        TrackingSession::create([
            'id' => $sid,
            'title' => 'Cmd Test',
            'start_time' => now(),
            'distance' => 10.5,
            'duration_seconds' => 3600,
            'status' => 'verified',
        ]);

        $this->artisan('lila:session-report --days=7')
            ->expectsOutputToContain('Session Report')
            ->expectsOutputToContain('Total sessions: 1')
            ->expectsOutputToContain('10.50 km')
            ->assertExitCode(0);
    }
}
