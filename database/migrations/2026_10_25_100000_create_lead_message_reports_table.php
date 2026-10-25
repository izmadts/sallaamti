<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sibling to nikah_reports, not a column added there — that table's
        // reported_profile_id is a mandatory FK to nikah_profiles, but a
        // reported counselor has no NikahProfile to point at. Keeps the
        // existing member-to-member report flow (NikahReport) untouched.
        Schema::create('lead_message_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reporter_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reported_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lead_client_message_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reason');
            $table->text('details')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_message_reports');
    }
};
