<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

// First real Policy class in the project — everything else is still ad-hoc
// abort_unless()/can: scattered across controllers (see Matchmaker\
// ClientController, Admin\LeadController, Api\V1\Matchmaker\ClientController,
// which each have their own near-identical copy of the logic below; not
// retrofitted to use this yet, left as a TODO for a future cleanup pass).
// Only new code (Api\V1\NikahMatchmakingController) uses this from day one.
class LeadPolicy
{
    // Full admins bypass the assigned_to scoping; leads.manage grants the
    // same cross-matchmaker visibility without the rest of the admin role
    // — the "Senior Matchmaker" ability.
    public function manageTeam(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('leads.manage');
    }

    // True for the counselor side (assigned counselor, or anyone with
    // team-wide visibility) OR the client side (the lead's own linked
    // member account) — one combined ability so new code doesn't need two
    // separate checks for "can this user see this lead."
    public function view(User $user, Lead $lead): bool
    {
        if ($this->manageTeam($user)) {
            return true;
        }

        if ((int) $lead->assigned_to === (int) $user->id) {
            return true;
        }

        return $lead->nikahProfile && (int) $lead->nikahProfile->user_id === (int) $user->id;
    }

    // Alias of view() — used at call sites where the client is responding
    // to something (a proposal, a consent request) rather than just
    // viewing, for readability at the call site.
    public function respond(User $user, Lead $lead): bool
    {
        return $this->view($user, $lead);
    }

    // A plain counselor's own client is permanently theirs — reassigning
    // it away is what left them 403'd trying to open a client they created
    // themselves. A client created by an admin or a leads.manage holder (a
    // "Senior Matchmaker") stays freely reassignable, since neither of
    // those is a specific counselor losing "their" client.
    public function reassign(User $user, Lead $lead): bool
    {
        if ($this->manageTeam($user)) {
            return true;
        }

        $creator = $lead->createdBy;
        $lockedToCreator = $creator !== null && !$creator->hasRole('admin') && !$creator->can('leads.manage');

        return !$lockedToCreator;
    }
}
