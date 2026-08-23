<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Replaces the old 2-value Public/Private switch with 4 real tiers —
// see App\Models\NikahProfile's visibility-related helpers for what each
// one actually gates. "Private" today means "hidden from member browse,
// but matchmakers still see it" (Matchmaker\NikahBrowseController never
// filtered on visibility at all), so existing private profiles map onto
// the new "matchmaker_assisted" tier, the closest match to their current
// real-world behavior — nobody's profile becomes MORE or LESS visible
// than it already was as a side effect of this migration.
return new class extends Migration
{
    public function up(): void
    {
        // Raw SQL rather than a Blueprint ->change() — MySQL ENUM columns
        // don't round-trip cleanly through Doctrine DBAL's type mapping
        // (same reasoning as the community_posts status migration).
        // Widen first so both old and new values are valid while the data
        // migrates, then narrow to the final set.
        DB::statement("ALTER TABLE nikah_profiles MODIFY visibility ENUM('public', 'private', 'members_only', 'matchmaker_assisted', 'confidential') NOT NULL DEFAULT 'public'");

        DB::table('nikah_profiles')->where('visibility', 'private')->update(['visibility' => 'matchmaker_assisted']);

        DB::statement("ALTER TABLE nikah_profiles MODIFY visibility ENUM('public', 'members_only', 'matchmaker_assisted', 'confidential') NOT NULL DEFAULT 'public'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE nikah_profiles MODIFY visibility ENUM('public', 'private', 'members_only', 'matchmaker_assisted', 'confidential') NOT NULL DEFAULT 'public'");

        DB::table('nikah_profiles')->whereIn('visibility', ['members_only', 'matchmaker_assisted', 'confidential'])->update(['visibility' => 'private']);

        DB::statement("ALTER TABLE nikah_profiles MODIFY visibility ENUM('public', 'private') NOT NULL DEFAULT 'public'");
    }
};
