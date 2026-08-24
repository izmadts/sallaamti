<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Admin-configurable commission rate table (project_matchmaker_hiring_
    // document's "don't hard-code $commission = 200" rule). Two rule
    // shapes share this table: 'verified_profile' (the flat Rs.1,000
    // verification fee, not tied to any NikahPackage) and 'package' (tied
    // to whichever NikahPackage admin actually created — Assisted/Premium/
    // whatever gets added later, never a hardcoded pair). Rate varies by
    // tier (any certified matchmaker's MatchmakerApplication::LEVELS,
    // defaulting to the base tier for anyone without one — see
    // CommissionLedgerEntry::tierFor()) and, for package rules, by
    // whether it's a first purchase or a renewal (doc's rule 8 — renewals
    // pay less than acquisitions).
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('rule_type', ['verified_profile', 'package']);
            $table->foreignId('nikah_package_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('tier');
            $table->boolean('is_renewal')->default(false);
            $table->enum('rate_type', ['percentage', 'fixed']);
            $table->decimal('rate_value', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['rule_type', 'nikah_package_id', 'tier', 'is_renewal'], 'commission_rules_unique_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rules');
    }
};
