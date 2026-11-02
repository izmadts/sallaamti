<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nikah_profile_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viewer_profile_id')->constrained('nikah_profiles')->cascadeOnDelete();
            $table->foreignId('viewed_profile_id')->constrained('nikah_profiles')->cascadeOnDelete();
            // One row per viewer/viewed pair, same as nikah_saved_profiles —
            // a repeat view updates view_count/last_viewed_at in place
            // rather than piling up a new row per visit.
            $table->unsignedInteger('view_count')->default(1);
            $table->timestamp('last_viewed_at');
            $table->timestamps();

            $table->unique(['viewer_profile_id', 'viewed_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nikah_profile_views');
    }
};
