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
        // nikah_reports
        Schema::create('nikah_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_profile_id')->constrained('nikah_profiles')->cascadeOnDelete();
            $table->foreignId('reported_profile_id')->constrained('nikah_profiles')->cascadeOnDelete();
            $table->string('reason');
            $table->text('details')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'dismissed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nikah_reports');
    }
};
