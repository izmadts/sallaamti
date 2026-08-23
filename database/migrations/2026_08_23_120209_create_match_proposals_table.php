<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // One candidate within a proposal batch (spec doc §23-24). Do NOT
    // store a numeric compatibility percentage here for customer display
    // (spec doc §18 — "do not display '91% perfect match' to customers,
    // internal decision-support only") — match_reasons is the
    // customer-visible "why this profile was shortlisted" checklist text,
    // kept deliberately separate from internal_notes.
    public function up(): void
    {
        Schema::create('match_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_profile_id')->constrained('nikah_profiles')->cascadeOnDelete();
            $table->json('match_reasons')->nullable();
            $table->text('internal_notes')->nullable();
            $table->enum('status', ['pending', 'sent', 'viewed', 'responded'])->default('pending');
            $table->enum('response', ['interested', 'not_interested', 'maybe', 'need_more_information', 'no_response'])->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['proposal_batch_id', 'candidate_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_proposals');
    }
};
