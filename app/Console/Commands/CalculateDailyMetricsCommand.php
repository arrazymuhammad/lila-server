<?php

namespace App\Console\Commands;

use App\Models\TrackingSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/** TASK-202: php artisan lila:calculate-daily-metrics */
class CalculateDailyMetricsCommand extends Command
{
    protected $signature = 'lila:calculate-daily-metrics {--date= : Date in Y-m-d format (defaults to yesterday)}';
    protected $description = 'Calculate and store daily summary metrics for analytics';

    public function handle(): int
    {
        $dateStr = $this->option('date') ?: now()->subDay()->format('Y-m-d');

        $sessions = TrackingSession::whereDate('start_time', $dateStr);
        $total = $sessions->count();
        $distance = (float) $sessions->sum('distance');
        $duration = (int) $sessions->sum('duration_seconds');
        $verified = TrackingSession::whereDate('start_time', $dateStr)->where('status', 'verified')->count();

        DB::table('daily_sync_summary')->updateOrInsert(
            ['date' => $dateStr],
            [
                'total_sessions' => $total,
                'total_distance' => $distance,
                'total_duration' => $duration,
                'verified_sessions' => $verified,
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, CURRENT_TIMESTAMP)'),
            ]
        );

        $this->info("Daily metrics calculated for {$dateStr}: {$total} sessions, {$verified} verified.");
        return Command::SUCCESS;
    }
}
