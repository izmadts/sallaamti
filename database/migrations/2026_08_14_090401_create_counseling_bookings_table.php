<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counseling_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_query_id')->nullable()->constrained('support_queries')->nullOnDelete();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->enum('status', ['requested', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('requested');
            $table->enum('contact_method', ['phone', 'video', 'in_person', 'chat'])->default('phone');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();

            $table->index(['counselor_id', 'scheduled_at']);
            $table->index(['member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counseling_bookings');
    }
};
