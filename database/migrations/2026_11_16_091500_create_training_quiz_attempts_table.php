<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// One row per quiz submission — kept even after a pass so "see my score any
// time" (owner's explicit ask) has real history, not just the latest
// attempt. No updated_at: an attempt is a fact at a point in time, same
// convention as MatchmakingTimelineEvent.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matchmaker_application_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('score');
            $table->unsignedInteger('total');
            $table->boolean('passed');
            $table->json('answers');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_quiz_attempts');
    }
};
