<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admissions were unique per (course, account) — one child per family
        // per course. Widen to (course, account, student name) so siblings
        // can both apply for the same course under one parent account.
        // MySQL won't drop an index that's the only one covering a foreign
        // key's leading column, so the replacement unique index (which also
        // starts with quran_live_course_id) has to exist before the old one
        // can go — otherwise the FK on quran_live_course_id is left uncovered
        // mid-migration and the DROP is rejected.
        Schema::table('quran_admissions', function (Blueprint $table) {
            $table->unique(['quran_live_course_id', 'user_id', 'student_name'], 'quran_admissions_course_user_student_unique');
        });
        Schema::table('quran_admissions', function (Blueprint $table) {
            $table->dropUnique(['quran_live_course_id', 'user_id']);
        });

        // Monthly fee payment was tied to the account, not to a specific
        // child — two siblings in the same course would collide on one
        // payment record. Move the subscription to be keyed by admission
        // (i.e. by child) instead.
        Schema::table('quran_subscriptions', function (Blueprint $table) {
            $table->foreignId('quran_admission_id')->nullable()->after('user_id')
                ->constrained()->nullOnDelete();
        });

        // Backfill: under the old constraint there was at most one admission
        // per (course, account), so this match is unambiguous for every
        // existing row.
        DB::table('quran_subscriptions as s')
            ->join('quran_admissions as a', function ($join) {
                $join->on('a.quran_live_course_id', '=', 's.quran_live_course_id')
                    ->on('a.user_id', '=', 's.user_id');
            })
            ->update(['s.quran_admission_id' => DB::raw('a.id')]);

        // Same FK-coverage issue as above: the new unique doesn't start with
        // quran_live_course_id or user_id, so plain indexes on those columns
        // have to go in first or their foreign keys are left uncovered when
        // the old composite unique is dropped.
        Schema::table('quran_subscriptions', function (Blueprint $table) {
            $table->index('quran_live_course_id');
            $table->index('user_id');
        });
        Schema::table('quran_subscriptions', function (Blueprint $table) {
            $table->dropUnique(['quran_live_course_id', 'user_id', 'month']);
            $table->unique(['quran_admission_id', 'month'], 'quran_subscriptions_admission_month_unique');
        });
    }

    public function down(): void
    {
        // Mirror image of up(): the FK on quran_admission_id has to be
        // dropped before its only covering index (the admission+month
        // unique) can go, same MySQL constraint-coverage rule as above.
        Schema::table('quran_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['quran_admission_id']);
        });
        Schema::table('quran_subscriptions', function (Blueprint $table) {
            $table->dropUnique('quran_subscriptions_admission_month_unique');
            $table->unique(['quran_live_course_id', 'user_id', 'month']);
        });
        // Leaving the plain quran_live_course_id/user_id indexes from up()
        // in place rather than fighting MySQL's FK-index-coverage rules
        // further — they're harmless, redundant-but-valid secondary indexes.
        Schema::table('quran_subscriptions', function (Blueprint $table) {
            $table->dropColumn('quran_admission_id');
        });

        Schema::table('quran_admissions', function (Blueprint $table) {
            $table->unique(['quran_live_course_id', 'user_id']);
        });
        Schema::table('quran_admissions', function (Blueprint $table) {
            $table->dropUnique('quran_admissions_course_user_student_unique');
        });
    }
};
