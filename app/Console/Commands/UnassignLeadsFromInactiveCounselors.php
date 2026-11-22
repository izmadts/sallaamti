<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\MatchmakerApplication;
use App\Models\User;
use Illuminate\Console\Command;

// One-time backfill (owner, 22 Nov 2026): "in ma boht sari lead aisi hain jo
// deactive/Rejected NC ko assign hoi pari hain unko kesy unassign karo ab
// live per?" — the 21 Nov auto-unassign fix (Lead::unassignAllFor(), wired
// into reject()/withdraw()/deactivateCounselor()/toggleActive()) only fires
// going forward, on the ACTION of rejecting/withdrawing/deactivating. It
// can't retroactively catch a counselor who was already in one of those
// states before that fix shipped — there's no "re-deactivate" button for an
// already-deactivated account. This command sweeps current live data once
// to catch up: every lead still assigned to a counselor who is inactive, or
// whose application is rejected/withdrawn, gets freed back into the
// unassigned pool. Safe to run more than once — a counselor with no such
// leads left is just a no-op.
class UnassignLeadsFromInactiveCounselors extends Command
{
    protected $signature = 'leads:unassign-inactive-counselors {--dry-run : List what would change without saving anything}';

    protected $description = 'One-time cleanup: unassign every lead still held by a deactivated, rejected, or withdrawn counselor';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $inactiveUserIds = User::where('is_active', false)->pluck('id');
        $terminalUserIds = MatchmakerApplication::whereIn('status', ['rejected', 'withdrawn'])
            ->whereNotNull('user_id')
            ->pluck('user_id');

        $userIds = $inactiveUserIds->merge($terminalUserIds)->unique()->values();

        if ($userIds->isEmpty()) {
            $this->info('No inactive/rejected/withdrawn counselors found — nothing to do.');

            return self::SUCCESS;
        }

        $totalUnassigned = 0;
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            $openCount = Lead::where('assigned_to', $userId)->count();
            if ($openCount === 0) {
                continue;
            }

            $this->line(($dryRun ? '[dry-run] Would unassign ' : 'Unassigning ') . "{$openCount} lead(s) from {$user?->name} (user #{$userId})");

            if (!$dryRun) {
                $count = Lead::unassignAllFor($userId, 'Unassigned — one-time cleanup: counselor was already inactive/rejected/withdrawn.');
                $totalUnassigned += $count;
            } else {
                $totalUnassigned += $openCount;
            }
        }

        $this->info(($dryRun ? 'Would unassign ' : 'Unassigned ') . "{$totalUnassigned} lead(s) total across {$userIds->count()} counselor(s).");

        return self::SUCCESS;
    }
}
