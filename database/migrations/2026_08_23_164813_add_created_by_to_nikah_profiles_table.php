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
        Schema::table('nikah_profiles', function (Blueprint $table) {
            // Null = the member registered themselves (user_id already
            // identifies them as the owner). Set only for staff-assisted
            // walk-in registration — see
            // Admin\NikahProfileWizardController::finalize() — so it's
            // always clear whether a profile was self-service or
            // matchmaker/admin-entered, and by whom.
            $table->foreignId('created_by')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });
    }
};
