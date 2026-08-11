<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('nikah_profiles', 'is_demo')) {
            Schema::table('nikah_profiles', function (Blueprint $table) {
                // Tags seeded placeholder profiles used to avoid an empty
                // "0 verified profiles" homepage during cold start — lets them
                // be found and removed cleanly (and never confused for real members).
                $table->boolean('is_demo')->default(false)->after('verification_status')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('nikah_profiles', 'is_demo')) {
            Schema::table('nikah_profiles', function (Blueprint $table) {
                $table->dropColumn('is_demo');
            });
        }
    }
};
