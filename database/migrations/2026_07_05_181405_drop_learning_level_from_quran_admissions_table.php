<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quran_admissions', function (Blueprint $table) {
            $table->dropColumn('learning_level');
        });
    }

    public function down(): void
    {
        Schema::table('quran_admissions', function (Blueprint $table) {
            $table->string('learning_level')->nullable();
        });
    }
};
