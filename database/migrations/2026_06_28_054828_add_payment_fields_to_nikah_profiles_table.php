<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'submitted', 'confirmed', 'rejected'])->default('unpaid')->after('visibility');
            $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_status');
            $table->string('payment_method')->nullable()->after('payment_amount'); // JazzCash / EasyPaisa / Bank Transfer
            $table->string('payment_reference')->nullable()->after('payment_method'); // transaction ID
            $table->string('payment_screenshot')->nullable()->after('payment_reference');
            $table->text('payment_rejection_reason')->nullable()->after('payment_screenshot');
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_amount', 'payment_method', 'payment_reference', 'payment_screenshot', 'payment_rejection_reason', 'payment_confirmed_at']);
        });
    }
};
