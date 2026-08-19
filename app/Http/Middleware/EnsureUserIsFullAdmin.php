<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Stricter than the 'admin' alias (EnsureUserIsAdmin), which also lets in any
// staff role holding a granular permission (see App\Support\PermissionCatalog).
// Reserved for areas that were always meant to stay admin-role-only —
// Users (role/permission assignment), Settings, Integrations (live API
// credentials), Maintenance, and Bulk Messaging (mass communication using
// those same credentials) — since a narrow staff permission like
// "wall.manage" should never be enough to reach any of those.
class EnsureUserIsFullAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, "You don't have access to that page.");
        }

        return $next($request);
    }
}
