<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

/** TASK-169: Health check endpoint /up for uptime monitors */
class HealthController extends Controller
{
    public function __invoke()
    {
        try {
            DB::connection()->getPdo();
            $dbOk = true;
        } catch (\Exception $e) {
            $dbOk = false;
        }

        $status = $dbOk ? 200 : 503;

        return response()->json([
            'status' => $dbOk ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'database' => $dbOk,
        ], $status);
    }
}
