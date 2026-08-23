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
        Schema::create('nikah_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 8)->default('PKR');
            // null = one-time/lifetime (e.g. the base Verified package);
            // otherwise the package's active window in days from when a
            // matchmaker starts it on a client (see leads.package_started_at).
            $table->unsignedInteger('duration_days')->nullable();
            // Total proposals (candidates) that may be sent to this client
            // across the whole package window — null = no cap. Deliberately
            // a cap on proposals *sent*, never framed as guaranteed matches.
            $table->unsignedInteger('proposal_limit')->nullable();
            $table->string('consultant_level')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->string('color', 16)->default('teal');
            $table->string('icon', 8)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            // Can this package be assigned to a client at all right now —
            // distinct from show_on_public_page, so a package can be kept
            // assignable-by-admin-only while not yet publicly advertised
            // (the VIP tier's "don't launch it immediately" case).
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_public_page')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nikah_packages');
    }
};
