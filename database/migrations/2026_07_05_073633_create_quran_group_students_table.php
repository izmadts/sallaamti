<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quran_group_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quran_class_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quran_admission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('joined_date');
            $table->date('completed_date')->nullable();
            $table->enum('status', ['active', 'on_hold', 'completed', 'dropped'])->default('active');
            $table->timestamps();

            $table->unique(['quran_class_group_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_group_students');
    }
};
