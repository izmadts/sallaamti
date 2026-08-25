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
            $table->foreignId('pending_package_id')->nullable()->after('nikah_package_id')->constrained('nikah_packages')->nullOnDelete();
            $table->string('package_payment_method')->nullable()->after('pending_package_id');
            $table->string('package_payment_reference')->nullable()->after('package_payment_method');
            $table->string('package_payment_screenshot')->nullable()->after('package_payment_reference');
            // null = nothing submitted yet, submitted|confirmed|rejected
            $table->string('package_payment_status')->nullable()->after('package_payment_screenshot');
            $table->string('package_payment_rejection_reason')->nullable()->after('package_payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pending_package_id');
            $table->dropColumn(['package_payment_method', 'package_payment_reference', 'package_payment_screenshot', 'package_payment_status', 'package_payment_rejection_reason']);
        });
    }
};
