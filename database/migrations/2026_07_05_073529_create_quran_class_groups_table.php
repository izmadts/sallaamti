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
        Schema::create('quran_class_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quran_live_course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('group_name'); // e.g. "Group A - Morning", "Group B - Evening"
            $table->json('class_days'); // ["Mon","Wed","Fri"]
            $table->string('class_time'); // "6:00 PM - 6:45 PM"
            $table->string('timezone')->default('Asia/Karachi');
            $table->enum('gender', ['male', 'female', 'mixed'])->default('mixed');
            $table->integer('max_students')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_class_groups');
    }
};
