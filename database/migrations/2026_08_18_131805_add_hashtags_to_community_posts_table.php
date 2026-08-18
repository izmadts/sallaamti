<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            // Admin-entered hashtags/keywords for social auto-posting —
            // deliberately separate from the existing `tags` column, which
            // drives the Wall's category filter (Activity/Event/Sermon) and
            // has nothing to do with what gets posted externally.
            $table->text('hashtags')->nullable()->after('tags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropColumn('hashtags');
        });
    }
};
