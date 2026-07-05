<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

/** TASK-169 & TASK-248: Enhanced health check endpoint /up */
class HealthController extends Controller
{
    public function __invoke()
    {
        // Check DB
        try {
            DB::connection()->getPdo();
            $dbOk = true;
        } catch (\Exception $e) {
            $dbOk = false;
        }

        // Check Disk space (> 10MB free)
        $diskSpaceOk = false;
        try {
            $free = disk_free_space(storage_path());
            $diskSpaceOk = $free !== false && $free > 10485760;
        } catch (\Exception $e) {
            $diskSpaceOk = false;
        }

        $allOk = $dbOk && $diskSpaceOk;
        $status = $allOk ? 200 : 503;

        return response()->json([
            'status' => $allOk ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'checks' => [
                'database' => $dbOk,
                'disk_space' => $diskSpaceOk,
            ],
        ], $status);
    }
}
