<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    use RegistersMinimalUsers;

    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Could not sign in with ' . ucfirst($provider) . '. Please try again.');
        }

        $user = User::where('provider', $provider)->where('provider_id', $socialUser->getId())->first();

        if (!$user && $socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update(['provider' => $provider, 'provider_id' => $socialUser->getId()]);
            }
        }

        if (!$user) {
            $user = $this->createMinimalUser(
                $socialUser->getName() ?? $socialUser->getNickname() ?? 'Sallaamti User',
                $socialUser->getEmail(),
                null
            );
            $user->update(['provider' => $provider, 'provider_id' => $socialUser->getId()]);
        }

        if (!$user->email_verified_at && $socialUser->getEmail()) {
            $user->email_verified_at = now();
            $user->save();
        }

        Auth::login($user);

        session()->flash('conversion_event', 'user_login');

        return redirect(route('dashboard', absolute: false));
    }
}
