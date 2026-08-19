<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Both tables key a record to (group, user_id) — fine when one
        // account has at most one child, but if two siblings land in the
        // same group, they share a user_id and a teacher's assessment or
        // monthly report for one silently overwrites/merges with the
        // other's. Scope both to the specific child (admission) instead.
        Schema::table('quran_assessments', function (Blueprint $table) {
            $table->foreignId('quran_admission_id')->nullable()->after('user_id')
                ->constrained()->nullOnDelete();
        });
        Schema::table('quran_progress_reports', function (Blueprint $table) {
            $table->foreignId('quran_admission_id')->nullable()->after('user_id')
                ->constrained()->nullOnDelete();
        });

        // Backfill via QuranGroupStudent, which already links (group, user)
        // to the correct admission for every existing membership.
        DB::table('quran_assessments as a')
            ->join('quran_group_students as gs', function ($join) {
                $join->on('gs.quran_class_group_id', '=', 'a.quran_class_group_id')
                    ->on('gs.user_id', '=', 'a.user_id');
            })
            ->update(['a.quran_admission_id' => DB::raw('gs.quran_admission_id')]);

        DB::table('quran_progress_reports as r')
            ->join('quran_group_students as gs', function ($join) {
                $join->on('gs.quran_class_group_id', '=', 'r.quran_class_group_id')
                    ->on('gs.user_id', '=', 'r.user_id');
            })
            ->update(['r.quran_admission_id' => DB::raw('gs.quran_admission_id')]);

        // Widen the progress-report unique constraint so two siblings in the
        // same group don't collide on one month's report. Same index-order
        // dance as the sibling migration: create the new one before
        // dropping the old, since both share a leading column that a
        // foreign key depends on.
        Schema::table('quran_progress_reports', function (Blueprint $table) {
            $table->unique(['quran_class_group_id', 'user_id', 'quran_admission_id', 'month'], 'quran_progress_reports_group_user_admission_month_unique');
        });
        Schema::table('quran_progress_reports', function (Blueprint $table) {
            $table->dropUnique(['quran_class_group_id', 'user_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::table('quran_progress_reports', function (Blueprint $table) {
            $table->unique(['quran_class_group_id', 'user_id', 'month']);
        });
        Schema::table('quran_progress_reports', function (Blueprint $table) {
            $table->dropUnique('quran_progress_reports_group_user_admission_month_unique');
        });
        Schema::table('quran_progress_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quran_admission_id');
        });

        Schema::table('quran_assessments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quran_admission_id');
        });
    }
};
