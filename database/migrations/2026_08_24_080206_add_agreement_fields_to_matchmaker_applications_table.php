<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The applicant reviews and accepts the real Nikah Counselor Agreement
    // + NDA themselves (a signed link + last-7-digits-of-mobile check,
    // same pattern as Lead's progress link) — admin no longer just claims
    // this happened via the pipeline dropdown. See
    // Public\MatchmakerAgreementController.
    public function up(): void
    {
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            $table->string('agreement_link_token')->nullable()->unique()->after('counselor_code');
            $table->timestamp('agreement_accepted_at')->nullable()->after('agreement_link_token');
            $table->string('agreement_ip')->nullable()->after('agreement_accepted_at');
            $table->timestamp('nda_accepted_at')->nullable()->after('agreement_ip');
            $table->string('nda_ip')->nullable()->after('nda_accepted_at');
        });
    }

    public function down(): void
    {
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            $table->dropColumn(['agreement_link_token', 'agreement_accepted_at', 'agreement_ip', 'nda_accepted_at', 'nda_ip']);
        });
    }
};
