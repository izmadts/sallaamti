<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Found 22 Nov 2026 auditing a test failure: no migration in this repo
    // ever creates a 'learning_level' column (create_quran_admissions_table
    // has 'learned_quran_before' + 'selected_level'/'previous_level' from a
    // later migration, never this exact name) — it must have existed on
    // production from a migration that was since replaced without ever
    // being rolled back there. That made this migration fail outright on
    // any fresh database (a new dev setup, or the test suite's SQLite
    // :memory: DB) with "no such column" — and since SQLite wraps a whole
    // migration batch in one transaction, that single failure silently
    // rolled back every table created before it too, breaking the entire
    // test suite's database setup, not just this one. Guarded so this stays
    // a correct no-op everywhere the column was never there, while still
    // dropping it on production where it actually exists.
    public function up(): void
    {
        if (Schema::hasColumn('quran_admissions', 'learning_level')) {
            Schema::table('quran_admissions', function (Blueprint $table) {
                $table->dropColumn('learning_level');
            });
        }
    }

    public function down(): void
    {
        Schema::table('quran_admissions', function (Blueprint $table) {
            $table->string('learning_level')->nullable();
        });
    }
};
