<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// API-safe counterpart to EnsureUserIsActive — that one only runs in the
// web middleware group and logs out a session; a mobile client holding a
// Sanctum bearer token never passes through it, so an admin deactivating a
// user on the website previously had no effect at all on the member/NC
// apps (same gap AuthController::tokenResponse() now also closes at the
// login/token-issue step — this covers every request after that).
class EnsureUserIsActiveApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->is_active) {
            $user->currentAccessToken()?->delete();

            abort(403, __('db.Your account has been deactivated. Please contact support.'));
        }

        return $next($request);
    }
}
