<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Structured requirement profile (spec doc §10-12) — deliberately
    // separate from NikahProfile's own pref_* columns, which are the
    // client's self-set preferences from their own wizard. This is what
    // the MATCHMAKER curates with the client, with per-item priority
    // weighting, so search/shortlisting has something more structured
    // to work from than free-text WhatsApp notes.
    public function up(): void
    {
        Schema::create('matchmaking_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('nikah_profile_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaking_requirements');
    }
};
