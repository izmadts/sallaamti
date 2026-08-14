<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week')->nullable(); // 0 (Sun) - 6 (Sat); null when specific_date is set
            $table->date('specific_date')->nullable(); // one-off override/blackout for a single day
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['counselor_id', 'day_of_week']);
            $table->index(['counselor_id', 'specific_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselor_availabilities');
    }
};
