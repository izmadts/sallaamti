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
            // Supersedes the old plain-string `package` enum, which stays in
            // place untouched (historical data, nothing reads it going
            // forward) rather than being dropped. Nullable — the whole
            // matchmaking flow already works fine with no package assigned.
            $table->foreignId('nikah_package_id')->nullable()->after('package')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('nikah_package_id');
        });
    }
};
