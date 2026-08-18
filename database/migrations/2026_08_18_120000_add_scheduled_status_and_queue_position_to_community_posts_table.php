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
        DB::statement("ALTER TABLE community_posts MODIFY status ENUM('draft', 'scheduled', 'published') NOT NULL DEFAULT 'draft'");

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

        DB::statement("ALTER TABLE community_posts MODIFY status ENUM('draft', 'published') NOT NULL DEFAULT 'draft'");
    }
};
