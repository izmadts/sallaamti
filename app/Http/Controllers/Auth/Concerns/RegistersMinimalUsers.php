<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait RegistersMinimalUsers
{
    protected function createMinimalUser(string $name, ?string $email, ?string $phone, ?string $password = null): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password ?? Str::random(40)),
        ]);

        $user->assignRole('member');

        event(new Registered($user));

        return $user;
    }
}
