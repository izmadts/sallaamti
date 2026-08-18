<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_posts', function (Blueprint $table) {
            $table->id();
            $table->string('saveable_type');
            $table->unsignedBigInteger('saveable_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // A user can only save a given post once — same shape as the
            // one-reaction-per-user unique constraint on reactions.
            $table->unique(['saveable_type', 'saveable_id', 'user_id']);
            $table->index(['saveable_type', 'saveable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_posts');
    }
};
