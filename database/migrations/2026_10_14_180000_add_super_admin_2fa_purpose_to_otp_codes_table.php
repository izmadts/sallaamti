<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Same strict-enum gotcha the password_reset migration fixed —
// LoginRequest::completeLogin() saves OtpCode rows with
// purpose = 'super_admin_2fa' for a Super Admin's second login factor,
// which would otherwise throw "Data truncated for column 'purpose'".
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') { // SQLite test DB: no ENUM to widen
            DB::statement("ALTER TABLE otp_codes MODIFY purpose ENUM('registration', 'login', 'password_reset', 'super_admin_2fa') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') { // SQLite test DB: no ENUM to widen
            DB::statement("ALTER TABLE otp_codes MODIFY purpose ENUM('registration', 'login', 'password_reset') NOT NULL");
        }
    }
};
