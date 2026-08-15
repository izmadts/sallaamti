<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized.');
        }

        if (!$request->user()->hasRole('admin')) {
            // A stale `url.intended` (e.g. from trying this URL before
            // logging in) would otherwise strand a legitimate member here
            // again on their very next login — clear it and send them
            // somewhere that actually exists for their role instead of a
            // dead-end 403.
            session()->forget('url.intended');

            return redirect()->route('dashboard')
                ->with('error', "You don't have access to that page.");
        }

        return $next($request);
    }
}
