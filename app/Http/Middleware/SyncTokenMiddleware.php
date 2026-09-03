<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * TASK-101: API Token Authentication for mobile sync
 *
 * Simple shared-secret token auth via X-Sync-Token header.
 * Mobile app sends this header on every sync request.
 * Server validates against SYNC_TOKEN env variable.
 */
class SyncTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Sync-Token');
        $expected = config('services.sync.token', '');

        if ($expected === '') {
            // SYNC_TOKEN not configured — log warning but allow (dev mode)
            \Illuminate\Support\Facades\Log::warning('SYNC_TOKEN not configured in .env');
            return $next($request);
        }

        if ($token === null || !hash_equals($expected, $token)) {
            \Illuminate\Support\Facades\Log::warning('API sync unauthorized', [
                'ip' => $request->ip(),
                'ua' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'UNAUTHORIZED',
                'message' => 'Token autentikasi tidak valid',
                'timestamp' => now()->toIso8601String(),
            ], 401);
        }

        return $next($request);
    }
}
