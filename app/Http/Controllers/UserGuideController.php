<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserGuideController extends Controller
{
    // Every logged-in user gets the Member tab; role-specific tabs only
    // show up for people who actually hold that role, so a member never
    // sees an "Admin Guide" tab that wouldn't apply to them, and an admin
    // (who can reach every area of the app) sees all of them.
    public function index()
    {
        $user = Auth::user();

        $canSeeAdminGuide = $user->hasRole('admin') || $user->getAllPermissions()->isNotEmpty();
        // A full Admin can reach every area of the app regardless of which
        // specific staff roles they personally hold, so they see every
        // guide tab too — useful for training staff or troubleshooting a
        // workflow that isn't their own day-to-day role.
        $isFullAdmin = $user->hasRole('admin');

        return view('guide.index', [
            'showAdmin' => $canSeeAdminGuide,
            'showMatchmaker' => $isFullAdmin || $user->hasRole('matchmaker'),
            'showTeacher' => $isFullAdmin || $user->hasRole('teacher'),
            'showCounselor' => $isFullAdmin || $user->hasRole('counselor'),
            'showContentManager' => $user->hasAnyRole(['admin', 'manager', 'blogger']),
        ]);
    }
}
