<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Stores one row per device a user has logged in from (Firebase Cloud
// Messaging registration token), so a push can be sent to every device
// they're signed in on — not just the most recent one. Scaffolding for the
// Nikah Counselor app's notification bell; actual sending needs a Firebase
// service-account key wired into a future PushNotificationService before
// any row here is used for real delivery.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token')->unique();
            $table->string('platform')->nullable(); // android, ios
            $table->string('app')->default('nikah_counselor'); // which app registered it
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
