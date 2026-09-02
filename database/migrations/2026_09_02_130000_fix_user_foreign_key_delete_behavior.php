<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Deleting a user is only safe once these "who did this" / staff-role
// foreign keys stop defaulting to RESTRICT (raw SQL crash the moment any
// row exists) or, worse, CASCADE (silently destroying a client's own data
// just because a staff member who merely created/recorded it got deleted).
// All twelve become nullOnDelete: the audit-trail/ownership pointer is
// cleared, but the actual record — someone else's course, requirement,
// proposal batch, moderation note, or support reply — survives untouched.
// See App\Services\UserDeletionService, which this unblocks.
//
// Constraint names are looked up from information_schema rather than
// assumed via Laravel's default `{table}_{column}_foreign` convention —
// a production database whose schema history diverged even slightly from
// this repo's migration order can legitimately have these auto-named
// differently, and guessing wrong throws a hard "doesn't exist" error
// before ever reaching the actual nullable/nullOnDelete change.
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
            foreach ($columns as $column) {
                $this->dropForeignKeysOn($tableName, $column);
            }

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
            foreach ($columns as $column) {
                $this->dropForeignKeysOn($tableName, $column);
            }

            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->foreign($column)->references('id')->on('users');
                }
            });
        }
    }

    // Drops every FK constraint currently on $table.$column, whatever it's
    // actually named — a no-op if there isn't one, which is exactly right
    // for an environment where this column was never constrained at all.
    private function dropForeignKeysOn(string $table, string $column): void
    {
        $database = DB::getDatabaseName();

        $constraints = DB::select(
            'SELECT DISTINCT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$database, $table, $column]
        );

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
        }
    }
};
