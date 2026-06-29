<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donation_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('donor_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('purpose')->nullable(); // General, Quran Education, Orphan Support, Nikah Fund, etc
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('payment_screenshot')->nullable();
            $table->enum('payment_status', ['submitted', 'confirmed', 'rejected'])->default('submitted');
            $table->text('payment_rejection_reason')->nullable();
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
