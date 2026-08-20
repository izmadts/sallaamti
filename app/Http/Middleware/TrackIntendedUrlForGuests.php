<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Remembers whatever page a guest is browsing so that if they click Register
// or Log In from anywhere on the public site, every existing
// redirect()->intended() call (already used across every auth-completion
// path — login, register, OTP, social auth, email verification) sends them
// straight back to it, instead of the generic dashboard. One central place
// for this instead of manually stamping session(['url.intended' => ...])
// on every public page that happens to link to register/login.
class TrackIntendedUrlForGuests
{
    // Routes that are themselves part of the auth flow — capturing these as
    // the "intended" destination would send someone back to a login/verify
    // page after logging in, instead of wherever they actually meant to go.
    private const EXCLUDED_ROUTE_PATTERNS = [
        'login', 'register', 'logout',
        'password.*', 'verification.*', 'otp.*', 'social.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (
            !$request->user()
            && $request->isMethod('get')
            && !$request->ajax()
            && !$request->wantsJson()
            && !$request->routeIs(self::EXCLUDED_ROUTE_PATTERNS)
        ) {
            $request->session()->put('url.intended', $request->fullUrl());
        }

        return $next($request);
    }
}
