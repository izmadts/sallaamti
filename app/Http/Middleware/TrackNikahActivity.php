<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackNikahActivity
{
    // Touches last_active_at at most once every 5 minutes per profile — cheap
    // enough to run on every Nikah request without hammering the DB on each click.
    public function handle(Request $request, Closure $next): Response
    {
        $profile = Auth::user()?->nikahProfile;

        if ($profile && (!$profile->last_active_at || $profile->last_active_at->lt(now()->subMinutes(5)))) {
            $profile->update(['last_active_at' => now()]);
        }

        return $next($request);
    }
}
