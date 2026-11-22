<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Raw SQL rather than a Blueprint ->change() — MySQL ENUM columns
        // don't round-trip cleanly through Doctrine DBAL's type mapping.
        // Guarded (22 Nov 2026, same pattern as
        // 2026_11_14_090000_add_hot_and_paid_lead_statuses.php) — this raw
        // MySQL ALTER...MODIFY syntax fails outright on SQLite (a fresh dev
        // setup, or the test suite's :memory: DB), which — since SQLite
        // wraps a whole migration batch in one transaction — silently
        // rolled back every table created before it too, not just this one.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE community_posts MODIFY status ENUM('draft', 'scheduled', 'published') NOT NULL DEFAULT 'draft'");
        }

        Schema::table('community_posts', function (Blueprint $table) {
            // Only meaningful while status = scheduled — drag-and-drop order
            // in the admin queue page, and the order the daily batch drains it.
            $table->unsignedInteger('queue_position')->nullable()->after('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropColumn('queue_position');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE community_posts MODIFY status ENUM('draft', 'published') NOT NULL DEFAULT 'draft'");
        }
    }
};
