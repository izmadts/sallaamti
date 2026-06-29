<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nikah_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_profile_id')->constrained('nikah_profiles')->cascadeOnDelete();
            $table->foreignId('receiver_profile_id')->constrained('nikah_profiles')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            // Prevent duplicate interest from same sender to same receiver
            $table->unique(['sender_profile_id', 'receiver_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nikah_interests');
    }
};
