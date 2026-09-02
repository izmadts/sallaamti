<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Same CNIC-photo policy change as the member Nikah profile: stop
 * collecting CNIC front/back images everywhere, including the Nikah
 * Counselor application (NikahCounselorApplicationController::store()).
 * CNIC number stays on the form looking mandatory but the backend no
 * longer enforces it. All three columns were NOT NULL (cnic_number also
 * unique) — nullable here so a submission without them doesn't fail at
 * the DB layer once the validation rule requiring them is gone.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            $table->string('cnic_number')->nullable()->change();
            $table->string('cnic_front_image')->nullable()->change();
            $table->string('cnic_back_image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('matchmaker_applications', function (Blueprint $table) {
            $table->string('cnic_back_image')->nullable(false)->change();
            $table->string('cnic_front_image')->nullable(false)->change();
            $table->string('cnic_number')->nullable(false)->change();
        });
    }
};
