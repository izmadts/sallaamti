<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dua_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_anonymous')->default(false);
            // Pre-publish moderation, not post-and-report — this is public UGC
            // under the Sallaamti name, so a slow feed is a much smaller cost
            // than something inappropriate going live.
            $table->string('status')->default('pending'); // pending|approved|hidden
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dua_requests');
    }
};
