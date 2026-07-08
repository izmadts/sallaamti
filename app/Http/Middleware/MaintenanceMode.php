<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAdmin = $request->user()?->hasRole('admin');

        if (setting('maintenance_mode') === '1' && !$isAdmin && !$request->routeIs('login', 'admin.*')) {
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
