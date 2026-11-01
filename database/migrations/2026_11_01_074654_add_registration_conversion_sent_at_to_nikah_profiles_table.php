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
            // Guards MetaConversionsApi against firing the same Meta
            // standard event twice for one profile — the API's submit()
            // (mobile app) has no "already submitted" guard of its own and
            // can be called again every time the member revisits the
            // review screen, and recordOffline() (admin, website) has no
            // guard either, unlike confirm()/bulkConfirm()'s own
            // payment_status check.
            $table->timestamp('registration_conversion_sent_at')->nullable()->after('verification_status');
            $table->timestamp('payment_conversion_sent_at')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropColumn(['registration_conversion_sent_at', 'payment_conversion_sent_at']);
        });
    }
};
