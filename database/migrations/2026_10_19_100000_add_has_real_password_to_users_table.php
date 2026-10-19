<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// False means this account's `password` column is a random, never-shown
// string (RegistersMinimalUsers::createMinimalUser()'s Str::random(40)
// fallback) rather than something the user or an admin actually knows —
// e.g. a phone+OTP self-signup, before the member sets a real one. Lets
// the "Change Password" form skip demanding a current_password that
// literally cannot exist yet, instead of permanently locking these
// accounts out of ever setting one themselves.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_real_password')->default(true)->after('must_change_password');
        });

        // Backfill existing phone+OTP self-signups that haven't already
        // been flagged must_change_password by some other (known-password)
        // flow — these are the accounts actually stuck in the gap above.
        DB::table('users')
            ->where('provider', 'whatsapp')
            ->where('must_change_password', false)
            ->update(['has_real_password' => false]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_real_password');
        });
    }
};
