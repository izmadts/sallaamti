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
        Schema::create('quran_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quran_class_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // student
            $table->enum('type', ['weekly_quiz', 'monthly_test', 'quarterly_exam', 'annual_exam']);
            $table->decimal('score', 5, 2)->nullable(); // out of 100
            $table->string('grade')->nullable(); // A, B, C, etc.
            $table->text('remarks')->nullable();
            $table->date('assessment_date');
            $table->foreignId('recorded_by')->constrained('users'); // teacher
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_assessments');
    }
};
