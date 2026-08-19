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

        // Which contact channel a user actually has on file — the thing
        // that determines whether an Email or WhatsApp broadcast can reach
        // them at all, as opposed to the free-text email/phone filters
        // above which just narrow by content.
        if ($request->filled('contact')) {
            if ($request->contact === 'has_email') {
                $query->whereNotNull('email')->where('email', '!=', '');
            } elseif ($request->contact === 'has_phone') {
                $query->whereNotNull('phone')->where('phone', '!=', '');
            }
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
