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
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->change();
            $table->string('type')->default('course')->after('course_id');
            $table->string('title')->nullable()->after('type');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['issued_by']);
            $table->dropColumn(['type', 'title', 'issued_by']);
            $table->foreignId('course_id')->nullable(false)->change();
        });
    }
};
