<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->string('marital_status')->default('never_married')->change();
        });
    }

    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->enum('marital_status', ['never_married', 'divorced', 'widowed', 'separate', 'married'])->default('never_married')->change();
        });
    }
};
