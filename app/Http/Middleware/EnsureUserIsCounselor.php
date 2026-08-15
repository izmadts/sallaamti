<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCounselor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized.');
        }

        if (!$request->user()->hasRole('counselor')) {
            session()->forget('url.intended');

            return redirect()->route('dashboard')
                ->with('error', "You don't have access to that page.");
        }

        return $next($request);
    }
}
