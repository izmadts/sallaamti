<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Passive audit trail for every signed-link action a client (or
    // whoever clicked the link) takes — IP, best-effort city from that
    // IP, device fingerprint, and the gap between when the matchmaker
    // generated the link and when it was actually used. Never blocks the
    // action itself; this is purely for admin to investigate an anomaly
    // after the fact (spec doc §39: "who accessed this person's
    // information?"), not an enforcement mechanism.
    public function up(): void
    {
        Schema::create('matchmaking_link_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->string('purpose');
            $table->string('ip_address', 45)->nullable();
            $table->string('city')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('result')->nullable();
            $table->timestamp('accessed_at')->useCurrent();

            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaking_link_accesses');
    }
};
