<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// AuthController::passwordForgot()/passwordReset() (added in 09aa556) save
// OtpCode rows with purpose = 'password_reset', but the enum here was never
// widened past the original ('registration', 'login') pair — every call
// to that endpoint throws "Data truncated for column 'purpose'" today.
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE otp_codes MODIFY purpose ENUM('registration', 'login', 'password_reset') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE otp_codes MODIFY purpose ENUM('registration', 'login') NOT NULL");
    }
};
