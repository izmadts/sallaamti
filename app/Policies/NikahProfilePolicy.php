<?php

namespace App\Policies;

use App\Models\NikahProfile;
use App\Models\User;

// See LeadPolicy's class comment — first real Policy classes in the
// project, not yet a full conversion of every ad-hoc nikah.manage/
// nikah.view check elsewhere. Scoped to ownership + the staff abilities
// that gate access to a specific profile (not the self-service member
// checks already in NikahProfileController, which stay as-is).
class NikahProfilePolicy
{
    public function view(User $user, NikahProfile $profile): bool
    {
        if ($user->can('nikah.manage') || $user->can('nikah.view')) {
            return true;
        }

        return (int) $profile->user_id === (int) $user->id;
    }

    public function manage(User $user, NikahProfile $profile): bool
    {
        if ($user->can('nikah.manage')) {
            return true;
        }

        return (int) $profile->user_id === (int) $user->id;
    }
}
