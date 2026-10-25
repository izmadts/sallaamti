<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullable timestamp, not a plain boolean — doubles as an audit
        // trail of *when* a profile became matcher-visible, not just
        // whether. NikahProfile::scopeMatchmakerVisible() previously only
        // checked is_active/not-suspended/visibility, with no actual
        // eligibility gate — any active profile was visible to a
        // matchmaker regardless of verification status. Set by
        // Admin\NikahVerificationController::approve() (cleared on
        // reject()), same place verification_status flips to 'verified'.
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->timestamp('matchmaking_opt_in_at')->nullable()->after('verification_status');
        });

        // Backfill: every profile that was ALREADY verified before this
        // column existed must opt in immediately, or scopeMatchmakerVisible()
        // would silently drop every one of them from matchmaker visibility
        // the moment this deploys — not just newly-approved ones going
        // forward. Approval time itself isn't recoverable, so this uses
        // updated_at as the closest available timestamp.
        \DB::table('nikah_profiles')
            ->where('verification_status', 'verified')
            ->whereNull('matchmaking_opt_in_at')
            ->update(['matchmaking_opt_in_at' => \DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropColumn('matchmaking_opt_in_at');
        });
    }
};
