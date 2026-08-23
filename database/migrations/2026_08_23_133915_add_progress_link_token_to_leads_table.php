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
        Schema::table('leads', function (Blueprint $table) {
            // A standing, non-expiring link a matchmaker generates once and
            // hands to the client — regenerating swaps this token and kills
            // any previously-copied link, same mechanism as
            // match_proposals.link_token.
            $table->string('progress_link_token', 64)->nullable()->after('nikah_profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('progress_link_token');
        });
    }
};
