<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dua_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dua_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            // Stops the same member reacting "Ameen" to the same dua twice.
            $table->unique(['dua_request_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dua_reactions');
    }
};
