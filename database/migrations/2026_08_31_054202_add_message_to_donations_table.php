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
        Schema::table('donations', function (Blueprint $table) {
            // Both validated and set on $validated in
            // DonationController::store(), but neither had a column to
            // land in - "message" was silently discarded every submission,
            // and "is_anonymous" meant the anonymous-donation toggle on
            // the public form did nothing at all (every donor's identity
            // stayed visible regardless of what they checked).
            $table->text('message')->nullable()->after('purpose');
            $table->boolean('is_anonymous')->default(false)->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['message', 'is_anonymous']);
        });
    }
};
