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
        Schema::create('quran_progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quran_class_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // student
            $table->string('month'); // "2026-06"
            $table->integer('classes_attended')->default(0);
            $table->integer('classes_total')->default(0);
            $table->text('quran_progress')->nullable(); // where they reached
            $table->text('behavior')->nullable();
            $table->text('homework_completion')->nullable();
            $table->text('teacher_comments')->nullable();
            $table->enum('overall_rating', ['excellent', 'good', 'average', 'needs_improvement'])->nullable();
            $table->foreignId('written_by')->constrained('users'); // teacher
            $table->timestamps();

            $table->unique(['quran_class_group_id', 'user_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_progress_reports');
    }
};
