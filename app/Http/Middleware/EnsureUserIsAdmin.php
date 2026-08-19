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

        // Full admins always get in. A staff role holding only specific
        // granular permissions (see App\Support\PermissionCatalog) also
        // gets past this general gate — individual admin routes/pages
        // still enforce their own specific permission via `can:` middleware,
        // this just decides who's allowed into the /admin area at all.
        $canEnterAdminArea = $request->user()->hasRole('admin')
            || $request->user()->getAllPermissions()->isNotEmpty();

        if (!$canEnterAdminArea) {
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
