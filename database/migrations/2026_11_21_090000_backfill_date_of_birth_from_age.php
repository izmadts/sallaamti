<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\NikahProfile;

// Owner, 16-21 Nov 2026: noticed a lot of profiles with `age` set but
// `date_of_birth` NULL. date_of_birth is a newer field (see NikahProfile's
// own `booted()` comment — it replaced self-reported age with a real,
// verifiable birth date) — these are profiles created or last saved before
// that field existed on the form, so they were never asked for it.
//
// Can't recover the real day/month, only the year, from a stored integer
// age — "exactly N years ago from today" is used (not Jan 1 of the birth
// year) specifically so recomputing age from this fabricated date lands on
// the SAME age already on record, not one year off for anyone whose real
// birthday hasn't happened yet this year. Saved through Eloquent (not a
// raw DB::table() update) so NikahProfile::booted()'s own saving() hook
// re-derives `age` the normal way too — belt and suspenders that the two
// columns end up in agreement.
return new class extends Migration
{
    public function up(): void
    {
        NikahProfile::whereNull('date_of_birth')
            ->whereNotNull('age')
            ->chunkById(200, function ($profiles) {
                foreach ($profiles as $profile) {
                    $profile->date_of_birth = now()->subYears($profile->age)->toDateString();
                    $profile->save();
                }
            });
    }

    public function down(): void
    {
        // Not reversible — there's no record of which date_of_birth rows
        // were real vs. backfilled by this migration.
    }
};
