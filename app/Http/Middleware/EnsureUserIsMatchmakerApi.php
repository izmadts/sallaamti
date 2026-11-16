<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// API-safe counterpart to EnsureUserIsMatchmaker — that one redirects on
// failure (session()->forget + redirect()->route()), which sends a JSON
// client a 302+HTML Location instead of a clean error. This aborts with a
// JSON-negotiated 403 instead, for the Api\V1\Matchmaker\* route group.
//
// 'matchmaker_trainee' (15 Nov 2026 addendum) gets through this same gate
// — an application still at Training/Assessed/Probation has real, just
// narrower, access. Which specific actions a trainee can reach is enforced
// per-route by EnsureFeatureUnlocked, not here.
class EnsureUserIsMatchmakerApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user?->hasRole('matchmaker') || $user?->hasRole('matchmaker_trainee'), 403, __('db.You don\'t have access to this area.'));

        return $next($request);
    }
}
