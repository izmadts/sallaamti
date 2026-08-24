<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// API-safe counterpart to EnsureUserIsMatchmaker — that one redirects on
// failure (session()->forget + redirect()->route()), which sends a JSON
// client a 302+HTML Location instead of a clean error. This aborts with a
// JSON-negotiated 403 instead, for the Api\V1\Matchmaker\* route group.
class EnsureUserIsMatchmakerApi
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->hasRole('matchmaker'), 403, __('db.You don\'t have access to this area.'));

        return $next($request);
    }
}
