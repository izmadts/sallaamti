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
        Schema::table('match_proposals', function (Blueprint $table) {
            // The signed link itself no longer expires by time (it stays
            // valid until the client actually responds), so this is the
            // only way to kill a previously-issued link: regenerating swaps
            // the token, and the old copy stops matching.
            $table->string('link_token', 64)->nullable()->after('response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_proposals', function (Blueprint $table) {
            $table->dropColumn('link_token');
        });
    }
};
