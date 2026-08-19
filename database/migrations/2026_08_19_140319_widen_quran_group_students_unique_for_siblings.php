<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Membership was unique per (group, account) — structurally
        // impossible for two siblings to both be in the same group, since
        // they share an account. Key it by (group, child/admission) instead.
        // New unique goes in first since it shares the leading column with
        // the old one, which a foreign key depends on being covered.
        Schema::table('quran_group_students', function (Blueprint $table) {
            $table->unique(['quran_class_group_id', 'quran_admission_id'], 'quran_group_students_group_admission_unique');
        });
        Schema::table('quran_group_students', function (Blueprint $table) {
            $table->dropUnique(['quran_class_group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('quran_group_students', function (Blueprint $table) {
            $table->unique(['quran_class_group_id', 'user_id']);
        });
        Schema::table('quran_group_students', function (Blueprint $table) {
            $table->dropUnique('quran_group_students_group_admission_unique');
        });
    }
};
