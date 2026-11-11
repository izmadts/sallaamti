<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            // A hash, never the plaintext — the password an applicant
            // chose at apply time, before any account exists to actually
            // hold it. Consumed (and cleared) by
            // MatchmakerApplicationController::certify() the moment a
            // brand-new User account gets created for them; left untouched
            // (and never applied) when certify() instead matches an
            // existing account by email — that account's real password is
            // never silently overwritten by an unrelated form submission.
            $table->string('pending_password')->nullable()->after('terms_accepted');
        });
    }

    public function down(): void
    {
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            $table->dropColumn('pending_password');
        });
    }
};
