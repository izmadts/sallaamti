<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Deleting a user is only safe once these "who did this" / staff-role
// foreign keys stop defaulting to RESTRICT (raw SQL crash the moment any
// row exists) or, worse, CASCADE (silently destroying a client's own data
// just because a staff member who merely created/recorded it got deleted).
// All twelve become nullOnDelete: the audit-trail/ownership pointer is
// cleared, but the actual record — someone else's course, requirement,
// proposal batch, moderation note, or support reply — survives untouched.
// See App\Services\UserDeletionService, which this unblocks.
return new class extends Migration
{
    private array $fixes = [
        'courses' => ['created_by'],
        'quran_live_courses' => ['created_by'],
        'quran_daily_links' => ['posted_by'],
        'quran_progress_reports' => ['written_by'],
        'quran_assessments' => ['recorded_by'],
        'matchmaking_consents' => ['recorded_by', 'revoked_by'],
        'matchmaking_consent_requests' => ['requested_by'],
        'matchmaking_requirements' => ['created_by'],
        'proposal_batches' => ['matchmaker_id'],
        'nikah_moderation_notes' => ['admin_id'],
        'query_responses' => ['responder_id'],
    ];

    public function up(): void
    {
        foreach ($this->fixes as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->dropForeign([$column]);
                }
            });

            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->unsignedBigInteger($column)->nullable()->change();
                }
            });

            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->foreign($column)->references('id')->on('users')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->fixes as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->dropForeign([$column]);
                }
            });

            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->foreign($column)->references('id')->on('users');
                }
            });
        }
    }
};
