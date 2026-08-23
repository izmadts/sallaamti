<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matchmaking_requirement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matchmaking_requirement_id')->constrained()->cascadeOnDelete();
            $table->string('requirement_type');
            $table->string('requirement_value');
            $table->enum('priority', ['must_have', 'preferred', 'flexible'])->default('preferred');
            $table->unsignedTinyInteger('weight')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['matchmaking_requirement_id', 'priority'], 'mm_requirement_items_req_priority_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaking_requirement_items');
    }
};
