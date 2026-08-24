<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Attributes a self-service registration to whichever certified
    // counselor's referral link (?ref=MM-PK-000145 on /register) the
    // visitor actually arrived through. Deliberately captured only at the
    // three real self-service registration completions (Concerns\
    // TracksReferrals::attributeReferral(), called from
    // RegisteredUserController/OtpController/ResolvesSocialLogin) — never
    // inside RegistersMinimalUsers::createMinimalUser() itself, since
    // that same helper also creates accounts for walk-in clients an
    // admin/matchmaker registers in person, which must never be
    // attributed to a stale referral code sitting in that staff member's
    // own browser session.
    public function up(): void
    {
        Schema::create('matchmaker_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counselor_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('counselor_code');
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique('referred_user_id');
            $table->index('counselor_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaker_referrals');
    }
};
