<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quran_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quran_live_course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('guardian_name');
            $table->string('whatsapp_number');
            $table->string('country');
            $table->string('city_state')->nullable();
            $table->string('student_name');
            $table->enum('student_gender', ['male', 'female']);
            $table->integer('student_age');
            $table->string('education_grade')->nullable();
            $table->boolean('learned_quran_before')->default(false);
            $table->string('learning_level')->nullable(); // Noorani Qaida / Nazira / Hifz / Tajweed etc
            $table->json('preferred_days')->nullable();
            $table->string('preferred_time')->nullable();
            $table->enum('teacher_preference', ['male', 'female', 'no_preference'])->default('no_preference');
            $table->text('comments')->nullable();
            $table->boolean('declaration_accepted')->default(false);

            $table->timestamps();

            $table->unique(['quran_live_course_id', 'user_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('quran_admissions');
    }
};
