<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait RegistersMinimalUsers
{
    protected function createMinimalUser(string $name, ?string $email, ?string $phone, ?string $password = null, string $provider = 'password', bool $mustChangePassword = false): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password ?? Str::random(40)),
            'provider' => $provider,
            // No real password was ever chosen/known when $password is
            // omitted — the stored one is an unguessable random string, so
            // there's nothing legitimate a "current password" field could
            // ever check against for this account yet.
            'has_real_password' => $password !== null,
            'must_change_password' => $mustChangePassword,
        ]);

        $user->assignRole('member');

        event(new Registered($user));

        return $user;
    }

    // Same charset the "Client Login Password" Blade sections already
    // generate client-side (see admin/leads/show.blade.php and matchmaker/
    // clients/show.blade.php's 🎲 Generate button) — no ambiguous-looking
    // characters (no 0/O, 1/l/I), since this gets read off a phone screen
    // and typed/copy-pasted by a counselor handing it to a client over
    // WhatsApp. Shared here so both the web walk-in wizard (finalize()) and
    // the mobile walk-in API (ClientProfileController::store()) generate a
    // real, visible temp password the same way instead of each inventing
    // their own.
    protected function generateTempPassword(int $length = 10): string
    {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }
}
