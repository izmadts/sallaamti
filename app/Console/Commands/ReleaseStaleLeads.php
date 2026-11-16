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
    protected $signature = 'leads:release-stale {--dry-run : List what would be released without changing anything}';

    protected $description = 'Unassign open leads whose status hasn\'t changed in 5+ days, returning them to the pool';

    private const STALE_DAYS = 5;

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $cutoff = now()->subDays(self::STALE_DAYS);

        $candidates = Lead::with('assignedTo')
            ->whereNotNull('assigned_to')
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

            // Fixed 15 Nov 2026 (same day this command shipped) — the check
            // above only ever looked at who CREATED the lead, never who it's
            // ASSIGNED to. Most open leads sit on the owner's own admin
            // account (admin both created and self-assigned them), which
            // sailed straight through and would have been wiped to
            // "unassigned" tonight — 848 of them. A lead only genuinely sits
            // "with a counselor" (the thing this command exists to detect)
            // when the assignee IS a counselor — has the matchmaker role and
            // isn't also an admin/staff account.
            //
            // 'matchmaker_trainee' (same-day trainee-access addendum) gets
            // the same treatment — a Training/Assessed/Probation counselor
            // can sit on a lead just as a certified one can, and the point
            // of this command (don't let a lead go stale with no follow-up)
            // applies just as much to them.
            $assignee = $lead->assignedTo;
            if (!$assignee || (!$assignee->hasRole('matchmaker') && !$assignee->hasRole('matchmaker_trainee')) || $assignee->hasRole('admin')) {
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

            $previousCounselor = $assignee->name;

            if ($dryRun) {
                $this->line("Would release lead #{$lead->id} ({$lead->name}) — currently with {$previousCounselor}, stale since {$staleSince->toDateString()}.");
                $released++;
                continue;
            }

            $lead->update(['assigned_to' => null]);

            MatchmakingTimelineEvent::log(
                $lead,
                $lead->nikahProfile,
                'released_to_pool',
                "Released back to the unassigned pool — no status change in " . self::STALE_DAYS . " days (was with {$previousCounselor})."
            );

            $released++;
        }

        $this->info(($dryRun ? 'Would release ' : 'Released ') . "{$released} stale lead(s) back to the pool.");

        return self::SUCCESS;
    }
}
