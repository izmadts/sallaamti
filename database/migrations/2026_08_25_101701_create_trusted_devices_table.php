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
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Opaque random value stored in the browser's own long-lived
            // cookie — the cookie itself carries no user identity, only
            // this token, which is looked up server-side against the
            // specific user being authenticated. Established the moment a
            // real password (or social) login succeeds; PIN-only login
            // (App\Http\Requests\Auth\LoginRequest) requires a matching
            // row here for that user, otherwise falls back to requiring
            // the real password.
            $table->string('token', 64);
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trusted_devices');
    }
};
