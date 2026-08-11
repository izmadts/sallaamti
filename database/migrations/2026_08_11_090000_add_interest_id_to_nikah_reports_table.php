<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nikah_reports', function (Blueprint $table) {
            $table->foreignId('nikah_interest_id')->nullable()->after('reported_profile_id')
                ->constrained('nikah_interests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('nikah_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('nikah_interest_id');
        });
    }
};
