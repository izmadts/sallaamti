<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $rules = ['password' => ['required', Password::defaults(), 'confirmed']];

        // A real current_password only exists to check once the account has
        // one — a phone+OTP self-signup's stored password is an unguessable
        // random string nobody, including the user, has ever seen, so
        // demanding it here would make this form impossible to ever use.
        if ($request->user()->has_real_password) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validateWithBag('updatePassword', $rules);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
            'has_real_password' => true,
        ]);

        return back()->with('status', 'password-updated');
    }
}
