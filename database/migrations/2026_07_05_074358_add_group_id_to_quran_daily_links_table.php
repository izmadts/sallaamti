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
        Schema::table('quran_daily_links', function (Blueprint $table) {
            $table->foreignId('quran_class_group_id')->nullable()->after('quran_live_course_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quran_daily_links', function (Blueprint $table) {
            //
        });
    }
};
