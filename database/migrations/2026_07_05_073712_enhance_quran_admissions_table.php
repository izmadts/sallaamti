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
        Schema::table('quran_admissions', function (Blueprint $table) {
            $table->string('selected_level')->nullable()->after('comments'); // which level they want
            $table->string('previous_level')->nullable()->after('selected_level'); // if continuing
            $table->enum('class_type', ['one_to_one', 'group'])->default('group')->after('previous_level');
            $table->string('timezone')->nullable()->after('class_type');
            $table->enum('status', ['pending', 'assigned', 'rejected'])->default('pending')->after('timezone');
            $table->text('admin_notes')->nullable()->after('status');
            $table->foreignId('assigned_group_id')->nullable()->constrained('quran_class_groups')->nullOnDelete()->after('admin_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quran_admissions', function (Blueprint $table) {
            //
        });
    }
};
