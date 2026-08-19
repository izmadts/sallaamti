<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

// Shared between Admin\UserManagementController::index (the user list page)
// and Admin\BulkMessageController::create (resolving who a broadcast should
// go to) — the same filters must narrow both, or "message the users I just
// filtered" could silently target a different set than what the admin saw.
class UserFilter
{
    public static function apply(Builder $query, Request $request): Builder
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('provider')) {
            $request->provider === 'unknown'
                ? $query->whereNull('provider')
                : $query->where('provider', $request->provider);
        }

        return $query;
    }
}
