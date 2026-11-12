<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Owner, 12 Nov 2026: new lead statuses (Account Registered, Pending Payment,
// Verified, Matched, Number Not Valid), then a one-time refresh of existing
// leads from their account/profile (App\Console\Commands\SyncLeadStatuses).
// Raw SQL for the ENUM, same reason as expand_nikah_profile_visibility_tiers.
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return; // SQLite test DB: no ENUM to widen
        }
        DB::statement("ALTER TABLE leads MODIFY status ENUM('new', 'contacted', 'interested', 'account_registered', 'registered', 'pending_payment', 'verified', 'matched', 'not_interested', 'number_not_valid', 'closed') NOT NULL DEFAULT 'new'");

        Artisan::call('leads:sync-statuses');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::table('leads')->whereIn('status', ['account_registered'])->update(['status' => 'contacted']);
        DB::table('leads')->whereIn('status', ['pending_payment', 'verified', 'matched'])->update(['status' => 'registered']);
        DB::table('leads')->whereIn('status', ['number_not_valid'])->update(['status' => 'closed']);
        DB::statement("ALTER TABLE leads MODIFY status ENUM('new', 'contacted', 'interested', 'registered', 'not_interested', 'closed') NOT NULL DEFAULT 'new'");
    }
};
