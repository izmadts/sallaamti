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
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->boolean('has_children')->nullable()->after('marital_status');
            $table->unsignedTinyInteger('children_count')->nullable()->after('has_children');
            $table->string('living_situation')->nullable()->after('children_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropColumn(['has_children', 'children_count', 'living_situation']);
        });
    }
};
