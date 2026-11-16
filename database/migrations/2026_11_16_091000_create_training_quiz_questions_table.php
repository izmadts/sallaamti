<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Trainee-access addendum (15/16 Nov 2026) — bilingual MCQ bank behind the
// in-app Training Guide, seeded by TrainingQuizQuestionSeeder. 'options' is
// an array of {text_en, text_ur}; correct_index points into it. Grading is
// server-side only (TrainingQuizController) — correct_index never leaves
// this table.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_en');
            $table->text('question_ur');
            $table->json('options');
            $table->unsignedTinyInteger('correct_index');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_quiz_questions');
    }
};
