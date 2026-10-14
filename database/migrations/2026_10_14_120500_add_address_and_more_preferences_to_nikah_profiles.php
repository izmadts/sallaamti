<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->string('address')->nullable()->after('city');
            $table->string('pref_income')->nullable()->after('pref_marital_status');
            $table->string('pref_other', 1000)->nullable()->after('pref_income');
        });
    }

    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropColumn(['address', 'pref_income', 'pref_other']);
        });
    }
};
