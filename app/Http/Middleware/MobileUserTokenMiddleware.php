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

            if ($user) {
                $request->attributes->set('mobile_user_id', $user->id);
                $request->attributes->set('mobile_user', $user);
                return $next($request);
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
