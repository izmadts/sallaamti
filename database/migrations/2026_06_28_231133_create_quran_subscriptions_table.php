<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quran_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quran_live_course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('month'); // "2026-06" format
            $table->decimal('amount', 10, 2);
            $table->enum('payment_status', ['unpaid', 'submitted', 'confirmed', 'rejected'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('payment_screenshot')->nullable();
            $table->text('payment_rejection_reason')->nullable();
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->timestamps();

            $table->unique(['quran_live_course_id', 'user_id', 'month']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('quran_subscriptions');
    }
};
