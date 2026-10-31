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
            // Nullable even though the form now requires it going forward —
            // every existing application was submitted before this column
            // existed, so backfilling a value here isn't possible.
            $table->string('email')->nullable()->after('mobile_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
