<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Called a "ledger," never a "wallet" — no money is ever custodied on
    // the platform (see project_matchmaker_hiring_status). Admin pays the
    // matchmaker manually (bank/JazzCash transfer) and marks the entry
    // Paid — this table exists purely to make that trail transparent to
    // both sides, per the hiring document's "just a few clicks" trace
    // requirement. Pending -> Approved (after the 7-day hold, manual, see
    // eligible_at) -> Paid. flagged_at/flag_reason coexist with 'pending'
    // status rather than being their own status, since a flag just blocks
    // approval, it doesn't change what stage the entry is at.
    public function up(): void
    {
        Schema::create('commission_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matchmaker_id')->constrained('users')->cascadeOnDelete();

            // The NikahProfile (verified_profile) or Lead (package/renewal)
            // this commission was earned from.
            $table->morphs('source');

            // 'recognition_bonus' is the discretionary, admin-granted award
            // for a documented successful outcome (hiring document rule
            // 51 — deliberately NOT an automatic commission tied to a
            // marriage happening, since there's no outcome-tracking data
            // source to trigger it safely off yet).
            $table->enum('rule_type', ['verified_profile', 'package', 'recognition_bonus']);
            $table->foreignId('nikah_package_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_renewal')->default(false);
            $table->string('tier_at_time');
            $table->string('rate_type');
            $table->decimal('rate_value', 8, 2);
            $table->decimal('base_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2);

            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->timestamp('eligible_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('flagged_at')->nullable();
            $table->string('flag_reason')->nullable();
            $table->foreignId('flagged_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['matchmaker_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_ledger_entries');
    }
};
