<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // A curated batch of 3-5 candidates (spec doc §21-22) — deliberately
    // never a raw list of everything that matched; the batch is the unit
    // a matchmaker reviews, sends, and tracks responses against as one
    // action, not per-candidate.
    public function up(): void
    {
        Schema::create('proposal_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('nikah_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('matchmaker_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('batch_number');
            $table->enum('status', ['draft', 'ready', 'sent', 'partially_responded', 'completed', 'expired', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['nikah_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_batches');
    }
};
