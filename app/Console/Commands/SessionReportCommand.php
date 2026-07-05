<?php

namespace App\Console\Commands;

use App\Models\TrackingSession;
use Illuminate\Console\Command;

/**
 * TASK-168: Generate a session status report.
 * php artisan lila:session-report {--status=verified} {--days=7}
 */
class SessionReportCommand extends Command
{
    protected $signature = 'lila:session-report
        {--status= : Filter by status}
        {--days=7 : Report period in days}';

    protected $description = 'Generate a session status report for the given period';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $status = $this->option('status');
        $since = now()->subDays($days);

        $query = TrackingSession::query()->where('start_time', '>=', $since);
        if ($status) {
            $query->where('status', $status);
        }

        $total = $query->count();
        $byStatus = (clone $query)->selectRaw('status, count(*) as cnt')->groupBy('status')->pluck('cnt', 'status');
        $totalDistance = (clone $query)->sum('distance');
        $totalDuration = (clone $query)->sum('duration_seconds');

        $this->info("Session Report ({$days}d, since {$since->toDateString()})");
        $this->newLine();

        $rows = $byStatus->map(fn($cnt, $st) => [$st, $cnt])->values()->toArray();
        $this->table(['Status', 'Count'], $rows);

        $this->info("Total sessions: {$total}");
        $this->info("Total distance: " . number_format($totalDistance, 2) . " km");
        $this->info("Total duration: {$totalDuration}s");

        if ($total > 0) {
            $this->info("Avg distance: " . number_format($totalDistance / $total, 2) . " km");
            $this->info("Avg duration: " . round($totalDuration / $total) . "s");
        }

        return Command::SUCCESS;
    }
}
