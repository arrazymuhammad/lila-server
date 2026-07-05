<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** TASK-119: API versioning header */
class ApiVersion
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('api/*')) {
            $response->headers->set('X-API-Version', 'v1');
        }

        return $response;
    }
}
