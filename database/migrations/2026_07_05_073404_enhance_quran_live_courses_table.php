<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quran_live_courses', function (Blueprint $table) {
            $table->string('level_number')->nullable()->after('title'); // Level 1, Level 2, etc.
            $table->string('duration')->nullable()->after('level_number'); // "6-12 Months"
            $table->json('topics')->nullable()->after('duration'); // syllabus topics array
            $table->text('outcome')->nullable()->after('topics');
            $table->string('category')->nullable()->after('outcome'); // Nazrah/Tajweed/Translation/Grammar/Advance/Special
            $table->enum('gender_preference', ['male', 'female', 'both'])->default('both')->after('category');
            $table->integer('max_students_per_group')->default(10)->after('gender_preference');
        });
    }
    public function down(): void
    {
        Schema::table('quran_live_courses', function (Blueprint $table) {
            $table->dropColumn(['level_number', 'duration', 'topics', 'outcome', 'category', 'gender_preference', 'max_students_per_group']);
        });
    }
};
