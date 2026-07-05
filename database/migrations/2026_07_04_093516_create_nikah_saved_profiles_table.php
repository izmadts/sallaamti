<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nikah_saved_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nikah_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('saved_profile_id')->constrained('nikah_profiles')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['nikah_profile_id', 'saved_profile_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('nikah_saved_profiles');
    }
};
