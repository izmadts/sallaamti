<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Owner, 14 Nov 2026: "Hot" leads on top priority (set by staff) and a
// "Paid" tag for leads whose profile is verified and whose fee was paid.
// Widens the ENUM, moves New leads that have a follow-up date to Follow-up,
// then refreshes existing leads so verified + paid ones
// move to Paid (fee-free verified profiles stay Verified).
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return; // SQLite test DB: no ENUM to widen
        }
        DB::statement("ALTER TABLE leads MODIFY status ENUM('hot', 'new', 'contacted', 'interested', 'account_registered', 'registered', 'pending_payment', 'paid', 'verified', 'matched', 'not_interested', 'number_not_valid', 'closed') NOT NULL DEFAULT 'new'");

        // Leads still "New" that already have a follow-up date were contacted
        // and are being followed up: show them as Follow-up (owner, 14 Nov).
        $ids = DB::table('leads')->where('status', 'new')->whereNotNull('next_follow_up_at')->pluck('id');
        foreach ($ids as $id) {
            DB::table('leads')->where('id', $id)->update(['status' => 'contacted', 'updated_at' => now()]);
            DB::table('matchmaking_timeline_events')->insert([
                'lead_id' => $id, 'event_type' => 'status_changed',
                'description' => 'Status changed automatically to Follow-up (has a follow-up date).',
                'created_at' => now(),
            ]);
        }

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
