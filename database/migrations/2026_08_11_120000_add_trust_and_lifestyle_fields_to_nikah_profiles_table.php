<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            // Suspend bug fix — a separate admin-only gate so a suspended
            // member's own "Activate My Profile" toggle can't undo it.
            $table->timestamp('suspended_at')->nullable()->after('is_active');
            $table->string('suspension_reason')->nullable()->after('suspended_at');

            // Deen / lifestyle compatibility fields
            $table->string('prayer_frequency')->nullable()->after('sect'); // always, usually, sometimes, rarely
            $table->string('hijab_or_beard')->nullable()->after('prayer_frequency'); // yes, no, sometimes
            $table->string('smokes')->nullable()->after('hijab_or_beard'); // no, occasionally, yes
            $table->string('diet')->nullable()->after('smokes'); // halal_only, halal_mostly, no_restriction

            // Ethnicity / language
            $table->string('ethnicity')->nullable()->after('country');
            $table->string('language')->nullable()->after('ethnicity');

            // Polygamy-intent (mainly relevant on female profiles browsing male
            // "married / second wife" status, but harmless on any profile)
            $table->boolean('open_to_polygamy')->nullable()->after('marital_status');

            // Trust badge stack — a third, independently-earned signal beyond
            // payment and CNIC: admin manually confirms the guardian contact
            // by phone and marks it here.
            $table->timestamp('guardian_verified_at')->nullable()->after('verification_status');

            // "Last active" — touched whenever the owner hits a Nikah route.
            $table->timestamp('last_active_at')->nullable()->after('guardian_verified_at');
        });

        // App-level fraud defense: a real person has exactly one CNIC.
        // Confirmed no existing duplicates before adding this (dev DB: 0/4).
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->unique('cnic_number');
        });
    }

    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropUnique(['cnic_number']);
            $table->dropColumn([
                'suspended_at',
                'suspension_reason',
                'prayer_frequency',
                'hijab_or_beard',
                'smokes',
                'diet',
                'ethnicity',
                'language',
                'open_to_polygamy',
                'guardian_verified_at',
                'last_active_at',
            ]);
        });
    }
};
