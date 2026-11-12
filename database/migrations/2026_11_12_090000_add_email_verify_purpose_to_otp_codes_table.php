<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Same strict-enum gotcha the password_reset and super_admin_2fa migrations
// fixed — AuthController's new emailResend()/register() save OtpCode rows
// with purpose = 'email_verify', which the enum here never allowed.
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE otp_codes MODIFY purpose ENUM('registration', 'login', 'password_reset', 'super_admin_2fa', 'email_verify') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE otp_codes MODIFY purpose ENUM('registration', 'login', 'password_reset', 'super_admin_2fa') NOT NULL");
    }
};
