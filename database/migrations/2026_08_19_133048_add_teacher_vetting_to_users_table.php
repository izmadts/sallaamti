<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('teacher_vetting_status')->default('pending')->after('counselor_bio');
            $table->text('teacher_vetting_notes')->nullable()->after('teacher_vetting_status');
            $table->timestamp('teacher_vetted_at')->nullable()->after('teacher_vetting_notes');
        });

        // Grandfather in anyone already holding the teacher role today — they're
        // already running live classes, so this migration shouldn't lock any of
        // them out. Only teachers assigned *after* this point start at pending.
        DB::table('users')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', '=', 'App\\Models\\User');
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', 'teacher')
            ->update(['users.teacher_vetting_status' => 'approved', 'users.teacher_vetted_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['teacher_vetting_status', 'teacher_vetting_notes', 'teacher_vetted_at']);
        });
    }
};
