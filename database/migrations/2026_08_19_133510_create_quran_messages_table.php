<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quran_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quran_admission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();

            $table->index('quran_admission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quran_messages');
    }
};
