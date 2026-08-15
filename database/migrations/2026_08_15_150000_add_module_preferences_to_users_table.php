<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('nikah_module_enabled')->default(true)->after('deactivated_at');
            $table->boolean('quran_module_enabled')->default(true)->after('nikah_module_enabled');
            $table->boolean('counseling_module_enabled')->default(true)->after('quran_module_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nikah_module_enabled', 'quran_module_enabled', 'counseling_module_enabled']);
        });
    }
};
