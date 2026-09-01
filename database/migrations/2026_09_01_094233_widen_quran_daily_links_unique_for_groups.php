<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original unique index was (course, date) — one link per COURSE
     * per day, from before class groups existed. Teacher\QuranTeacherController
     * ::postDailyLink() has posted per-GROUP for a long time now (a course
     * can have several active groups on the same schedule), but the index
     * was never widened to match. The moment any course has a 2nd active
     * group, the second teacher to post their group's link on a given day
     * hits a raw unique-constraint violation, because both groups still
     * collide on the old (course, date) key.
     *
     * Every real write already carries quran_class_group_id (postDailyLink()
     * requires a group to post to; there is no code path that creates a
     * course-level, group-less link), so re-keying on (group, date) matches
     * what the application has actually been doing all along.
     *
     * MySQL won't drop an index while it's the only one covering a foreign
     * key's leading column. Both quran_live_course_id and
     * quran_class_group_id are FKs here and each currently relies on one of
     * the two unique indexes swapping places for that coverage, so plain
     * indexes go on both first — same reasoning as the sibling-support
     * migration for quran_admissions/quran_subscriptions.
     */
    public function up(): void
    {
        Schema::table('quran_daily_links', function (Blueprint $table) {
            $table->index('quran_live_course_id', 'quran_daily_links_course_id_index');
            $table->index('quran_class_group_id', 'quran_daily_links_group_id_index');
        });

        Schema::table('quran_daily_links', function (Blueprint $table) {
            $table->unique(['quran_class_group_id', 'class_date'], 'quran_daily_links_group_date_unique');
        });

        DB::statement('ALTER TABLE quran_daily_links DROP INDEX quran_daily_links_quran_live_course_id_class_date_unique');
    }

    public function down(): void
    {
        Schema::table('quran_daily_links', function (Blueprint $table) {
            $table->unique(['quran_live_course_id', 'class_date']);
        });

        DB::statement('ALTER TABLE quran_daily_links DROP INDEX quran_daily_links_group_date_unique');

        Schema::table('quran_daily_links', function (Blueprint $table) {
            $table->dropIndex('quran_daily_links_course_id_index');
            $table->dropIndex('quran_daily_links_group_id_index');
        });
    }
};
