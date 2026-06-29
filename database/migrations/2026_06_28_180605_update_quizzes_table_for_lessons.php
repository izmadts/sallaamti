<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('lesson_id')->nullable()->unique()->after('course_id')->constrained()->cascadeOnDelete();
        });

        // course_id needs to become nullable since a quiz might belong to a lesson instead
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['lesson_id']);
            $table->dropColumn('lesson_id');
            $table->foreignId('course_id')->nullable(false)->change();
        });
    }
};
