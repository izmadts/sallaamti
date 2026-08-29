<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\NikahProfile;
use Illuminate\Console\Command;

// One-off backfill for a bug in Admin\NikahProfileWizardController::finalize():
// before the fix, a walk-in registration only created a Lead when "remote
// verification" was checked, so any profile a counselor/admin registered
// in person (the common case) never got a Lead — meaning it never showed
// up in the counselor's My Clients workspace, and admin could never find
// it in /admin/leads to assign it to a counselor. This finds every
// staff-created profile still missing that Lead and creates one, exactly
// mirroring what finalize() now does going forward. Safe to re-run — it
// only ever touches profiles with zero matching Lead.
class BackfillMissingLeads extends Command
{
    protected $signature = 'nikah:backfill-leads {--dry-run : List what would be created without writing anything}';

    protected $description = 'Create a Lead for every staff-registered NikahProfile that never got one (walk-in registrations before the finalize() fix)';

    public function handle(): int
    {
        $linkedProfileIds = Lead::whereNotNull('nikah_profile_id')->pluck('nikah_profile_id');

        $orphans = NikahProfile::with('user')
            ->whereNotNull('created_by')
            ->whereNotIn('id', $linkedProfileIds)
            ->get();

        if ($orphans->isEmpty()) {
            $this->info('No orphaned profiles found — nothing to backfill.');

            return self::SUCCESS;
        }

        $this->info("Found {$orphans->count()} staff-registered profile(s) with no Lead:");

        foreach ($orphans as $profile) {
            $user = $profile->user;
            $this->line("  Profile #{$profile->id} — {$user?->name} (registered by user #{$profile->created_by})");

            if ($this->option('dry-run')) {
                continue;
            }

            Lead::create([
                'name' => $user?->name ?? 'Unknown',
                'gender' => $user?->gender,
                'phone' => $user?->phone,
                'email' => $user?->email,
                'status' => 'registered',
                'assigned_to' => $profile->created_by,
                'nikah_profile_id' => $profile->id,
                'source' => 'manual',
                'created_by' => $profile->created_by,
            ]);
        }

        if ($this->option('dry-run')) {
            $this->comment('Dry run — nothing was created. Re-run without --dry-run to apply.');
        } else {
            $this->info('Backfill complete.');
        }

        return self::SUCCESS;
    }
}
