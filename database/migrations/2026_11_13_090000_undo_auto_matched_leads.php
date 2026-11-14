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
        Artisan::call('leads:sync-statuses', ['--fix-matched' => true]);
    }

    public function down(): void
    {
        // Nothing to undo: the old automatic Matched was wrong.
    }
};
