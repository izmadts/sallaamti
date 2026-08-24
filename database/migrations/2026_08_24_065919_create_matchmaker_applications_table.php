<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The public "Become a Nikah Counselor" application — deliberately
    // usable by a total stranger with no Sallaamti account yet (mirrors
    // VolunteerApplication's guest-friendly shape), NOT the matchmaker
    // role itself. A real User account (RegistersMinimalUsers) and the
    // 'matchmaker' Spatie role only get attached once status reaches
    // 'certified' — see project_matchmaker_hiring_document's "don't allow
    // instant → dashboard" rule. user_id is set immediately if they
    // happened to be logged in when applying, same as VolunteerApplication.
    public function up(): void
    {
        Schema::create('matchmaker_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('full_name');
            $table->string('guardian_name');
            $table->string('mobile_number');
            $table->string('whatsapp_number')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->unsignedTinyInteger('age');
            $table->string('marital_status');
            $table->string('qualification');
            $table->string('qualification_other')->nullable();

            $table->string('selfie_photo');
            $table->string('cnic_number')->unique();
            $table->string('cnic_front_image');
            $table->string('cnic_back_image');

            $table->string('area')->nullable();
            $table->text('address')->nullable();

            // Where their commission gets paid out, manually by admin —
            // see project_matchmaking_crm_status's note that this is a
            // ledger, not a real e-wallet; no payment gateway involved.
            $table->enum('payout_method', ['bank_transfer', 'jazzcash', 'easypaisa'])->nullable();
            $table->string('payout_account_title')->nullable();
            $table->string('payout_account_number')->nullable();
            $table->string('payout_bank_name')->nullable();

            $table->boolean('consent_accepted')->default(false);
            $table->boolean('terms_accepted')->default(false);

            // Passive fraud/audit capture, same spirit as
            // MatchmakingLinkAccess — never blocks the actual submission.
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_city')->nullable();

            $table->enum('status', [
                'applied', 'identity_verified', 'references_checked', 'interviewed',
                'agreement_signed', 'nda_signed', 'training', 'assessed', 'probation',
                'certified', 'rejected', 'withdrawn',
            ])->default('applied');

            // Public title tier — starts at the base "Nikah Counselor" brand
            // and is meant to visibly upgrade with performance (see the
            // still-to-build quality-score module); admin-adjustable for now.
            $table->enum('level', [
                'nikah_counselor', 'certified_nikah_counselor',
                'senior_nikah_counselor', 'regional_nikah_coordinator',
            ])->default('nikah_counselor');

            $table->text('notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('counselor_code')->nullable()->unique();
            $table->timestamp('certified_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaker_applications');
    }
};
