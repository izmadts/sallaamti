<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->integer('pref_min_age')->nullable()->after('expectations');
            $table->integer('pref_max_age')->nullable()->after('pref_min_age');
            $table->string('pref_city')->nullable()->after('pref_max_age');
            $table->string('pref_sect')->nullable()->after('pref_city');
            $table->string('pref_education')->nullable()->after('pref_sect');
            $table->string('pref_marital_status')->nullable()->after('pref_education');
        });
    }
    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropColumn(['pref_min_age', 'pref_max_age', 'pref_city', 'pref_sect', 'pref_education', 'pref_marital_status']);
        });
    }
};
