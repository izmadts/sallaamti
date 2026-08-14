<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    use RegistersMinimalUsers;

    // Client ID/secret can come from the admin settings panel or the .env
    // file — settings win when present so an admin can configure this
    // without touching the server. The redirect URI is always the app's
    // own route, never admin-editable, so it can't drift from what's
    // actually registered in the routes file.
    public static function isEnabled(string $provider): bool
    {
        $enabled = Setting::get("{$provider}_login_enabled", '1') !== '0';
        $clientId = Setting::get("{$provider}_client_id") ?: config("services.{$provider}.client_id");
        $clientSecret = Setting::get("{$provider}_client_secret") ?: config("services.{$provider}.client_secret");

        return $enabled && filled($clientId) && filled($clientSecret);
    }

    private function applyProviderConfig(string $provider): void
    {
        Config::set("services.{$provider}.client_id", Setting::get("{$provider}_client_id") ?: config("services.{$provider}.client_id"));
        Config::set("services.{$provider}.client_secret", Setting::get("{$provider}_client_secret") ?: config("services.{$provider}.client_secret"));
        Config::set("services.{$provider}.redirect", route('social.callback', $provider));
    }

    public function redirect(string $provider): RedirectResponse
    {
        if (!static::isEnabled($provider)) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' sign-in is not configured yet.');
        }

        $this->applyProviderConfig($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        if (!static::isEnabled($provider)) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' sign-in is not configured yet.');
        }

        $this->applyProviderConfig($provider);

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

        if ($user->isDeactivated()) {
            $user->reactivate();
            session()->flash('status', 'Welcome back! Your account has been reactivated.');
        }

        Auth::login($user);

        session()->flash('conversion_event', 'user_login');

        return redirect(route('dashboard', absolute: false));
    }
}
