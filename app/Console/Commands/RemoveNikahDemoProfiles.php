<?php

namespace App\Console\Commands;

use App\Models\NikahProfile;
use App\Models\User;
use Illuminate\Console\Command;

class RemoveNikahDemoProfiles extends Command
{
    protected $signature = 'nikah:demo-profiles-remove';

    protected $description = 'Remove all seeded demo Nikah profiles (and their placeholder user accounts) created by nikah:demo-profiles.';

    public function handle(): int
    {
        $userIds = NikahProfile::where('is_demo', true)->pluck('user_id');

        if ($userIds->isEmpty()) {
            $this->info('No demo profiles found — nothing to remove.');
            return self::SUCCESS;
        }

        // Deleting the demo users cascades to their nikah_profiles row, and
        // from there to photos/interests/saved/blocks/reports — all foreign
        // keys in this module are already set up with cascadeOnDelete().
        User::whereIn('id', $userIds)->delete();

        $this->info("Removed {$userIds->count()} demo Nikah profile(s).");

        return self::SUCCESS;
    }
}
