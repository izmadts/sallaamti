<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// The admission's status only ever tracked pending/assigned/rejected — there
// was nowhere for it to reflect a student later dropping or completing a
// course. Admin\QuranClassGroupAdminController::updateStudentStatus() changed
// QuranGroupStudent.status but had no admission-level status to sync it to,
// so an admission could sit forever reading "assigned" and pointing at a
// group the student was actually dropped from. Widening the enum here is
// what makes that sync (added alongside this migration) possible.
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE quran_admissions MODIFY status ENUM('pending', 'assigned', 'rejected', 'completed', 'dropped') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE quran_admissions MODIFY status ENUM('pending', 'assigned', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};
