<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('min_age')->nullable()->after('level');
            $table->unsignedTinyInteger('max_age')->nullable()->after('min_age');
        });

        Schema::table('quran_live_courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('min_age')->nullable()->after('level_number');
            $table->unsignedTinyInteger('max_age')->nullable()->after('min_age');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['min_age', 'max_age']);
        });

        Schema::table('quran_live_courses', function (Blueprint $table) {
            $table->dropColumn(['min_age', 'max_age']);
        });
    }
};
