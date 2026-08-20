<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nikah_contact_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nikah_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, approved, denied
            $table->text('admin_notes')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index(['nikah_profile_id', 'requested_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nikah_contact_requests');
    }
};
