<?php

namespace App\Http\Middleware;

use App\Models\MobileUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MobileUserTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Mobile-Token');

        if ($token) {
            $hashedToken = hash('sha256', $token);
            $user = MobileUser::where('auth_token', $hashedToken)->first();

            if ($user && $user->is_active) {
                $request->attributes->set('mobile_user_id', $user->id);
                $request->attributes->set('mobile_user', $user);
                return $next($request);
            }

            // A deactivated account is a deliberate admin action to stop this user —
            // hard-reject rather than falling through to soft mode (which would let
            // their sync through anonymously, defeating the point of deactivating them).
            if ($user && !$user->is_active) {
                Log::warning('MobileUserTokenMiddleware: Deactivated account attempted sync', [
                    'mobile_user_id' => $user->id,
                    'ip' => $request->ip(),
                ]);

                return response()->json([
                    'success' => false,
                    'code' => 'ACCOUNT_DEACTIVATED',
                    'message' => 'Akun Anda telah dinonaktifkan. Hubungi admin.',
                ], 403);
            }

            Log::warning('MobileUserTokenMiddleware: Invalid X-Mobile-Token provided', [
                'ip' => $request->ip(),
                'token_preview' => substr($token, 0, 8) . '...'
            ]);
        } else {
            Log::info('MobileUserTokenMiddleware: Missing X-Mobile-Token header (Soft Mode)', [
                'ip' => $request->ip(),
            ]);
        }

        // Soft mode allows requests to proceed without authentication (attaches null user id)
        $request->attributes->set('mobile_user_id', null);
        $request->attributes->set('mobile_user', null);

        return $next($request);
    }
}
