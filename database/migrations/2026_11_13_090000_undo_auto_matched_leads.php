<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Owner, 13 Nov 2026: "Matched 19 wrong hai" — the first status refresh
// marked leads Matched because of accepted interests, but an accepted
// interest only shares contact details, it is not a rishta. Matched is now
// staff-only; this puts those auto-Matched leads back on their account /
// profile status (leads a person set to Matched by hand are left alone).
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // leads:sync-statuses runs today's SyncLeadStatuses, which can
        // compute 'hot'/'paid' (Lead::statusFromProfile()) — those aren't in
        // the leads.status ENUM yet on a fresh deploy (2026_11_14_090000
        // only widens it after this migration runs), so writing one here
        // truncates and throws (caught live while testing this merge: lead
        // #55 -> 'paid'). Widening here too makes that write safe; the next
        // migration's identical ALTER is then a harmless no-op.
        DB::statement("ALTER TABLE leads MODIFY status ENUM('hot', 'new', 'contacted', 'interested', 'account_registered', 'registered', 'pending_payment', 'paid', 'verified', 'matched', 'not_interested', 'number_not_valid', 'closed') NOT NULL DEFAULT 'new'");

        Artisan::call('leads:sync-statuses', ['--fix-matched' => true]);
    }

    public function down(): void
    {
        // Nothing to undo: the old automatic Matched was wrong.
    }
};
