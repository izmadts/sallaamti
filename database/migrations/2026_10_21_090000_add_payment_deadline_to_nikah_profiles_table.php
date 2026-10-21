<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A self-service member who never pays the verification fee gets daily
// reminders, then an automatic pause after the deadline passes — so an
// abandoned, never-verified profile doesn't sit in search/browse forever.
// payment_deadline_passed_at is distinct from a plain null/future deadline
// so the UI can tell "paused because the deadline passed" apart from the
// member's own separate voluntary is_active toggle, without guessing from
// payment_status + is_active alone.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->timestamp('payment_deadline_at')->nullable()->after('payment_rejection_reason');
            $table->timestamp('payment_deadline_passed_at')->nullable()->after('payment_deadline_at');
            // Capped at 3 (NikahProfile::MAX_PAYMENT_DEADLINE_EXTENSIONS) —
            // past that, self-service stops and the member is pointed at
            // WhatsApp support instead, so a profile can't sit unpaid and
            // un-verified in search indefinitely on repeat self-extensions.
            $table->unsignedTinyInteger('payment_deadline_extensions_used')->default(0)->after('payment_deadline_passed_at');
        });
    }

    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropColumn(['payment_deadline_at', 'payment_deadline_passed_at', 'payment_deadline_extensions_used']);
        });
    }
};
