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
        Schema::table('users', function (Blueprint $table) {
            // Explicit opt-in, not just "has a phone number" — WhatsApp
            // Business Platform requires consent before a business sends a
            // template message, or the sending number risks getting flagged.
            $table->boolean('whatsapp_notify_opt_in')->default(false)->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('whatsapp_notify_opt_in');
        });
    }
};
