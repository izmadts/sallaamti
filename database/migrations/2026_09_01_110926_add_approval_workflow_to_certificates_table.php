<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Course-completion certificates were instant self-service — the moment a
 * student finished a course, generateCertificate() minted a real
 * certificate_number and it was immediately downloadable, no review step.
 *
 * The other three ways a Certificate row gets created (Volunteer ID,
 * Nikah Counselor ID, an admin manually issuing one) already only happen as
 * the direct result of an admin action, so they default to 'approved' here
 * — this migration doesn't change their behavior at all. Only the course
 * flow (web CertificateController::generate / mobile LearningController::
 * generateCertificate) is changed elsewhere to actually create a 'pending'
 * row instead.
 *
 * certificate_number/issued_at become nullable because a pending course
 * request has neither yet — both are only assigned once an admin approves
 * it, so a still-pending or rejected request was never a real, publicly
 * verifiable certificate (see CertificateController::verify()).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('certificate_number')->nullable()->change();
            $table->timestamp('issued_at')->nullable()->change();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('type');
            $table->text('rejection_reason')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['status', 'rejection_reason']);
            $table->timestamp('issued_at')->nullable(false)->change();
            $table->string('certificate_number')->nullable(false)->change();
        });
    }
};
