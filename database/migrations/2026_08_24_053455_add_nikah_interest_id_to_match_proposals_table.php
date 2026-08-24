<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Bridges a matchmaker's proposal response into the platform's existing
    // self-service NikahInterest system (send/notify/accept/decline/contact
    // reveal already all built there) instead of building a parallel
    // mutual-interest mechanism. Nullable — only set when the response was
    // "interested" and the bridge actually succeeded (see
    // Concerns\SendsNikahInterest::sendInterestBetweenProfiles()).
    public function up(): void
    {
        Schema::table('match_proposals', function (Blueprint $table) {
            $table->foreignId('nikah_interest_id')->nullable()->after('response')->constrained('nikah_interests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('match_proposals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('nikah_interest_id');
        });
    }
};
