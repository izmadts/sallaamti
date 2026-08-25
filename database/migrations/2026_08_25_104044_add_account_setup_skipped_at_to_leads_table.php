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
            // Set when the client explicitly says "not now" to the
            // quick-access PIN offer on their progress link — without
            // this, the offer would resurface every visit for anyone who
            // deliberately doesn't want an account, which is exactly the
            // "don't disturb the customer" rule this whole page follows.
            $table->timestamp('account_setup_skipped_at')->nullable()->after('progress_link_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('account_setup_skipped_at');
        });
    }
};
