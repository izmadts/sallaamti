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
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            // 'withdrawn' has been a valid status value (and TERMINAL_EXIT_
            // STATUSES member, and index filter option) since this table was
            // created, but nothing could ever actually set it — updateStatus()
            // only accepts STEPS values, and reject() hard-codes 'rejected'.
            // This column backs the new withdraw() action that finally makes
            // it reachable.
            $table->timestamp('withdrawn_at')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            $table->dropColumn('withdrawn_at');
        });
    }
};
