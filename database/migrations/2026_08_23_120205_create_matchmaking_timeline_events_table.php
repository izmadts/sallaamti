<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The permanent, chronological audit trail for a lead/client's whole
    // matchmaking journey (spec doc §9) — every other new feature below
    // (requirements, proposals, link access) writes into this as it
    // happens, rather than the timeline being reconstructed after the
    // fact. Deliberately generic (event_type + description + meta) so it
    // doesn't need a schema change every time a new kind of event shows up.
    public function up(): void
    {
        Schema::create('matchmaking_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('nikah_profile_id')->nullable()->constrained()->cascadeOnDelete();
            // Null = system-generated (e.g. an automatic status change),
            // not attributed to a specific staff member's action.
            $table->foreignId('matchmaker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type');
            $table->text('description');
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['lead_id', 'created_at']);
            $table->index(['nikah_profile_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaking_timeline_events');
    }
};
