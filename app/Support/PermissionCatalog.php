<?php

namespace App\Support;

use Spatie\Permission\Models\Permission;

// Single source of truth for the granular admin permissions this app
// supports, on top of the existing 'admin' role (which stays a full
// unrestricted bypass — see AppServiceProvider's Gate::before hook).
// These exist so an admin can hand a staff member a role scoped to just
// "moderate the Wall" or "review donations" without also handing them the
// full 'admin' role's access to Users, Integrations, and Settings.
//
// Deliberately covers a first wave of content/moderation-style resources,
// not literally every admin area — Users (role assignment) and
// Integrations (live API credentials) stay 'admin'-role-only regardless of
// permissions, since those are privilege-escalation and credential-
// exposure risks respectively, not really "can this staff member edit a
// blog post" style access decisions. Extend RESOURCES below (and the
// matching route middleware in routes/web.php) to widen coverage later.
class PermissionCatalog
{
    public const RESOURCES = [
        'posts' => 'Public Posts (member/staff submissions, need approval)',
        'community-posts' => 'Community Posts (Wall content admin publishes)',
        'nikah' => 'Nikah Verifications & Moderation',
        'donations' => 'Donations',
        'volunteers' => 'Volunteer Applications',
        'wall' => 'Wall Moderation (member-submitted duas)',
        'counseling' => 'Family Counseling Bookings',
    ];

    public const ACTIONS = [
        'view' => 'View',
        'manage' => 'Create / Edit',
        'delete' => 'Delete',
    ];

    /** @return array<int, string> every permission name this catalog defines, e.g. "donations.delete" */
    public static function all(): array
    {
        $names = [];

        foreach (array_keys(self::RESOURCES) as $resource) {
            foreach (array_keys(self::ACTIONS) as $action) {
                $names[] = "{$resource}.{$action}";
            }
        }

        return $names;
    }

    // Idempotent — safe to call on every Roles page load rather than
    // requiring a separate `php artisan db:seed` step per environment
    // (this app has no automated deploy pipeline; a manual seed step is
    // one more thing to forget on the live server after a deploy).
    public static function ensureSeeded(): void
    {
        foreach (self::all() as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }
}
