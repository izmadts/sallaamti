<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\MatchmakingTimelineEvent;
use Illuminate\Console\Command;

// Backlog #1, point 6 (15 Nov 2026, owner agreed): a lead that's been
// sitting with a counselor for 5 days with no status change goes back to
// the unassigned pool, so a slow counselor doesn't just sit on a city's
// leads forever. Only "still open" statuses are eligible — a lead already
// Registered/Paid/Verified/etc. (Lead::NO_FOLLOW_UP_STATUSES) needs no more
// chasing, and a lead a counselor created for themselves (isLockedToCreator
// in Admin\LeadController/Matchmaker\ClientController) is never swept —
// that's their own client, not one handed to them from the pool.
class ReleaseStaleLeads extends Command
{
    protected $signature = 'leads:release-stale';

    protected $description = 'Unassign open leads whose status hasn\'t changed in 5+ days, returning them to the pool';

    private const STALE_DAYS = 5;

    public function handle(): int
    {
        $cutoff = now()->subDays(self::STALE_DAYS);

        $candidates = Lead::whereNotNull('assigned_to')
            ->whereNotIn('status', Lead::NO_FOLLOW_UP_STATUSES)
            ->get();

        $released = 0;

        foreach ($candidates as $lead) {
            // A lead created by a plain counselor (not admin/leads.manage)
            // is permanently theirs — same rule the admin/matchmaker
            // controllers already enforce on reassignment.
            $creator = $lead->createdBy;
            if ($creator && !$creator->hasRole('admin') && !$creator->can('leads.manage')) {
                continue;
            }

            $lastStatusChange = MatchmakingTimelineEvent::where('lead_id', $lead->id)
                ->where('event_type', 'status_changed')
                ->latest('created_at')
                ->value('created_at');

            $staleSince = $lastStatusChange ?? $lead->created_at;
            if ($staleSince->gt($cutoff)) {
                continue;
            }

            $previousCounselor = $lead->assignedTo?->name ?? 'the counselor';
            $lead->update(['assigned_to' => null]);

            MatchmakingTimelineEvent::log(
                $lead,
                $lead->nikahProfile,
                'released_to_pool',
                "Released back to the unassigned pool — no status change in " . self::STALE_DAYS . " days (was with {$previousCounselor})."
            );

            $released++;
        }

        $this->info("Released {$released} stale lead(s) back to the pool.");

        return self::SUCCESS;
    }
}
