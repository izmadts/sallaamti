<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            $table->timestamp('card_requested_at')->nullable()->after('counselor_code');
            $table->timestamp('card_dispatched_at')->nullable()->after('card_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            $table->dropColumn(['card_requested_at', 'card_dispatched_at']);
        });
    }
};
