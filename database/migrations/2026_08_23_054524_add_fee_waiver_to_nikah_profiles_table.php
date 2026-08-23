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
            $table->boolean('fee_waived')->default(false)->after('payment_rejection_reason');
            $table->foreignId('fee_waived_by')->nullable()->after('fee_waived')->constrained('users')->nullOnDelete();
            $table->string('fee_waived_reason')->nullable()->after('fee_waived_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropForeign(['fee_waived_by']);
            $table->dropColumn(['fee_waived', 'fee_waived_by', 'fee_waived_reason']);
        });
    }
};
