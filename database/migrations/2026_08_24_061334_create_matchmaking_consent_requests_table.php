<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // A pending "please confirm" ask a matchmaker sends a client through
    // their secure progress link — deliberately a separate table from
    // MatchmakingConsent (which stays an append-only record of actual
    // grants, always created with granted_at already set, exactly as
    // before). A real MatchmakingConsent row is only created once the
    // client actually responds "grant" here — matchmaker-recorded
    // verbal/phone/in-person consent (ClientController::recordConsent())
    // is untouched and still writes straight to MatchmakingConsent.
    public function up(): void
    {
        Schema::create('matchmaking_consent_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->string('consent_type');
            $table->foreignId('requested_by')->constrained('users');
            $table->timestamp('requested_at')->useCurrent();
            $table->enum('status', ['pending', 'granted', 'declined'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'consent_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaking_consent_requests');
    }
};
