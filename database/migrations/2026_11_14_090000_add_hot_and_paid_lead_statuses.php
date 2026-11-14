<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Owner, 14 Nov 2026: "Hot" leads on top priority (set by staff) and a
// "Paid" tag for leads whose profile is verified and whose fee was paid.
// Widens the ENUM, then refreshes existing leads so verified + paid ones
// move to Paid (fee-free verified profiles stay Verified).
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return; // SQLite test DB: no ENUM to widen
        }
        DB::statement("ALTER TABLE leads MODIFY status ENUM('hot', 'new', 'contacted', 'interested', 'account_registered', 'registered', 'pending_payment', 'paid', 'verified', 'matched', 'not_interested', 'number_not_valid', 'closed') NOT NULL DEFAULT 'new'");

        Artisan::call('leads:sync-statuses');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::table('leads')->where('status', 'hot')->update(['status' => 'contacted']);
        DB::table('leads')->where('status', 'paid')->update(['status' => 'verified']);
        DB::statement("ALTER TABLE leads MODIFY status ENUM('new', 'contacted', 'interested', 'account_registered', 'registered', 'pending_payment', 'verified', 'matched', 'not_interested', 'number_not_valid', 'closed') NOT NULL DEFAULT 'new'");
    }
};
