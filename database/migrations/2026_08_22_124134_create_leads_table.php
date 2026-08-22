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
        // Deliberately one table covering the whole lifecycle — WhatsApp
        // contact through ongoing matchmaking-service client — rather than
        // a separate "client" entity. Most Sallaamti leads never register
        // on their own; a matchmaker logs them here first, then converts
        // to a real Nikah profile via the existing walk-in wizard once
        // they're ready, without losing the trail of who they were before
        // that.
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('looking_for', ['self', 'family_member'])->nullable();
            $table->enum('source', ['facebook', 'instagram', 'whatsapp', 'website', 'phone', 'referral', 'manual', 'other'])->default('manual');
            $table->enum('status', ['new', 'contacted', 'interested', 'registered', 'not_interested', 'closed'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->date('next_follow_up_at')->nullable();

            // Set once the matchmaker actually converts this lead into a
            // real account + Nikah profile via the walk-in wizard.
            $table->foreignId('nikah_profile_id')->nullable()->constrained('nikah_profiles')->nullOnDelete();

            // Simple package/pricing tracking for the assisted-matchmaking
            // service itself — deliberately just a label + price rather
            // than a separate packages/entitlements system, since there's
            // no real usage data yet to know what limits/tiers actually
            // matter.
            $table->enum('package', ['none', 'verified_profile', 'assisted', 'premium', 'vip'])->default('none');
            $table->decimal('package_price', 10, 2)->nullable();
            $table->date('package_started_at')->nullable();
            $table->date('package_expires_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
