<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quran_daily_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quran_live_course_id')->constrained()->cascadeOnDelete();
            $table->date('class_date');
            $table->string('join_url');
            $table->string('passcode')->nullable();
            $table->foreignId('posted_by')->constrained('users');
            $table->timestamps();

            $table->unique(['quran_live_course_id', 'class_date']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('quran_daily_links');
    }
};
