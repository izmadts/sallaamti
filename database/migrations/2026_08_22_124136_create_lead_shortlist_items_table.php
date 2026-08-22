<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // A matchmaker's manually-curated candidate list for one lead —
        // deliberately not the spec's full proposal_batches/match_proposals/
        // consent apparatus. Sharing still happens over WhatsApp in
        // reality; this just gives the matchmaker one place to track who
        // they've already picked and shared, instead of losing that in
        // chat history.
        Schema::create('lead_shortlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nikah_profile_id')->constrained()->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['lead_id', 'nikah_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_shortlist_items');
    }
};
