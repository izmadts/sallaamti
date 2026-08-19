<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quran_group_students', function (Blueprint $table) {
            $table->date('last_class_reminder_date')->nullable()->after('status');
            $table->string('last_fee_reminder_month', 7)->nullable()->after('last_class_reminder_date');
        });
    }

    public function down(): void
    {
        Schema::table('quran_group_students', function (Blueprint $table) {
            $table->dropColumn(['last_class_reminder_date', 'last_fee_reminder_month']);
        });
    }
};
