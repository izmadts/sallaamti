<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Lets a wrongly-confirmed payment (profile fee or counselor package) be
// undone without leaving a stray commission entry that would otherwise
// silently become approved/paid after the 7-day hold — see
// Admin\NikahPaymentAdminController::reject() and
// Admin\LeadController::rejectPackagePayment().
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE commission_ledger_entries MODIFY status ENUM('pending', 'approved', 'paid', 'voided') DEFAULT 'pending'");

        Schema::table('commission_ledger_entries', function (Blueprint $table) {
            $table->timestamp('voided_at')->nullable()->after('flagged_by');
            $table->string('void_reason')->nullable()->after('voided_at');
            $table->foreignId('voided_by')->nullable()->after('void_reason')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('commission_ledger_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voided_by');
            $table->dropColumn(['voided_at', 'void_reason']);
        });

        DB::statement("ALTER TABLE commission_ledger_entries MODIFY status ENUM('pending', 'approved', 'paid') DEFAULT 'pending'");
    }
};
