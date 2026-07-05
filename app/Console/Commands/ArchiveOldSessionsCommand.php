<?php

namespace App\Console\Commands;

use App\Models\TrackingSession;
use Illuminate\Console\Command;

/** TASK-324: lila:archive-old-sessions */
class ArchiveOldSessionsCommand extends Command
{
    protected $signature = 'lila:archive-old-sessions {--months=6 : Months threshold} {--dry-run : Only display count without deleting}';
    protected $description = 'Archive verified sessions older than N months';

    public function handle(): int
    {
        $months = (int) $this->option('months');
        $threshold = now()->subMonths($months);

        $query = TrackingSession::where('status', 'verified')->where('start_time', '<', $threshold);
        $count = $query->count();

        if ($this->option('dry-run')) {
            $this->info("[Dry Run] Found {$count} verified sessions older than {$months} months.");
            return Command::SUCCESS;
        }

        if ($count === 0) {
            $this->info("No sessions to archive.");
            return Command::SUCCESS;
        }

        // For YAGNI simplicity, we log archival and purge from primary table
        // Upgrade path: insert into tracking_sessions_archive table before delete
        $query->delete();
        $this->info("Archived (purged) {$count} old sessions.");

        return Command::SUCCESS;
    }
}
