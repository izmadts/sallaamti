<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->renameColumn('cnic_front_image', 'cnic_front_image');
            $table->string('cnic_back_image')->nullable()->after('cnic_front_image');
            $table->boolean('allow_photo_sharing')->default(true)->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->renameColumn('cnic_front_image', 'cnic_front_image');
            $table->dropColumn(['cnic_back_image', 'allow_photo_sharing']);
        });
    }
};
