<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_bookings', function (Blueprint $table) {
            // Never shown to the member — counselor/admin case notes, kept
            // separate from `notes` (which the member does see) so a
            // counselor can write an honest clinical observation.
            $table->text('internal_notes')->nullable()->after('notes');
            $table->string('meeting_link')->nullable()->after('contact_method');
            $table->unsignedTinyInteger('member_rating')->nullable()->after('cancellation_reason');
            $table->text('member_feedback')->nullable()->after('member_rating');
        });

        Schema::table('users', function (Blueprint $table) {
            // Only meaningful for users holding the 'counselor' role — a
            // short specialty/bio line shown to members picking a counselor.
            $table->text('counselor_bio')->nullable()->after('whatsapp_notify_opt_in');
        });
    }

    public function down(): void
    {
        Schema::table('counseling_bookings', function (Blueprint $table) {
            $table->dropColumn(['internal_notes', 'meeting_link', 'member_rating', 'member_feedback']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('counselor_bio');
        });
    }
};
