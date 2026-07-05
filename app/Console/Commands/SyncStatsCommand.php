<?php

namespace App\Console\Commands;

use App\Models\TrackingSession;
use App\Models\ActivityEvent;
use Illuminate\Console\Command;

/** TASK-124: php artisan lila:sync-stats */
class SyncStatsCommand extends Command
{
    protected $signature = 'lila:sync-stats {--days=7}';
    protected $description = 'Display sync statistics for the past N days';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $since = now()->subDays($days);

        $totalSessions = TrackingSession::where('created_at', '>=', $since)->count();
        $submitted = TrackingSession::where('created_at', '>=', $since)->where('status', 'submitted')->count();
        $verified = TrackingSession::where('created_at', '>=', $since)->where('status', 'verified')->count();
        $rejected = TrackingSession::where('created_at', '>=', $since)->where('status', 'rejected')->count();
        $totalEvents = ActivityEvent::where('created_at', '>=', $since)->count();

        $this->info("=== LILA Sync Stats (last {$days} days) ===");
        $this->newLine();
        $this->table(['Metric', 'Value'], [
            ['Sessions synced', number_format($totalSessions)],
            ['Submitted (pending)', number_format($submitted)],
            ['Verified', number_format($verified)],
            ['Rejected', number_format($rejected)],
            ['Total findings', number_format($totalEvents)],
        ]);

        return Command::SUCCESS;
    }
}
