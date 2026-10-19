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
}
