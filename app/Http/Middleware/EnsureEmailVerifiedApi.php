<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Deliberately NOT built on Laravel's MustVerifyEmail/`verified` middleware —
// User doesn't implement that interface, so the web app's existing `verified`
// middleware (routes/web.php's dashboard/posts/testimonials groups) is
// already a silent no-op there; wiring MustVerifyEmail for real would start
// enforcing it everywhere at once, a much bigger behavior change than this
// is meant to be. This checks `email_verified_at` directly instead, scoped
// to only the handful of write endpoints a freshly-registered, not-yet-
// verified account could use to spam (see AuthController::register()/
// emailResend() for how the code is issued).
//
// A phone-only account (no email on file at all) isn't blocked here — it
// has nothing to verify by this route, and the OTP registration path it
// came through already proved control of the phone number.
class EnsureEmailVerifiedApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->email && !$user->email_verified_at) {
            // 'code' (16 Nov 2026 fix) — plain abort(403, $message) gave the
            // app only prose to show, with nowhere to actually act on it
            // (no screen anywhere in the app had an email-code entry field
            // reachable once already logged in). A stable machine-readable
            // code lets the app recognize this specific case and pop the
            // verification dialog itself, in any language, rather than
            // string-matching the translated message.
            return response()->json([
                'message' => __('db.Please verify your email before doing this — check your inbox for the code, or tap "Resend code" in the app.'),
                'code' => 'email_not_verified',
            ], 403);
        }

        return $next($request);
    }
}
