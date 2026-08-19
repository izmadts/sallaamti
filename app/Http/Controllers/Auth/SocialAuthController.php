<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    use RegistersMinimalUsers;

    // TikTok has no Socialite driver, so it can't go through the generic
    // {provider}_client_id-driven path below — it gets its own dedicated
    // methods. Its Setting keys are also deliberately separate from the
    // tiktok_client_id/secret used by the POSTING integration under Admin >
    // Integrations (a different TikTok Developer app) — see
    // SettingsController for the same split already done for Facebook.
    public static function isEnabled(string $provider): bool
    {
        if ($provider === 'tiktok') {
            $enabled = Setting::get('tiktok_login_enabled', '1') !== '0';
            $clientId = Setting::get('tiktok_login_client_id');
            $clientSecret = Setting::get('tiktok_login_client_secret');

            return $enabled && filled($clientId) && filled($clientSecret);
        }

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

        if ($provider === 'tiktok') {
            return $this->redirectToTiktok();
        }

        $this->applyProviderConfig($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request): RedirectResponse
    {
        if (!static::isEnabled($provider)) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' sign-in is not configured yet.');
        }

        if ($provider === 'tiktok') {
            return $this->handleTiktokCallback($request);
        }

        $this->applyProviderConfig($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Could not sign in with ' . ucfirst($provider) . '. Please try again.');
        }

        return $this->loginOrRegister(
            $provider,
            $socialUser->getId(),
            $socialUser->getName() ?? $socialUser->getNickname() ?? 'Sallaamti User',
            $socialUser->getEmail()
        );
    }

    // ===== TikTok — Login Kit, not Content Posting API (different scope,
    // different App), but the same underlying v2 OAuth endpoints TikTok
    // already uses for posting (see Admin\SocialIntegrationController) —
    // confirmed against developers.tiktok.com/doc/login-kit-web and the
    // OAuth token-management doc directly rather than assumed. =====

    private function redirectToTiktok(): RedirectResponse
    {
        $state = Str::random(32);
        session(['tiktok_login_oauth_state' => $state]);

        $query = http_build_query([
            'client_key' => Setting::get('tiktok_login_client_id'),
            'scope' => 'user.info.basic',
            'response_type' => 'code',
            'redirect_uri' => route('social.callback', 'tiktok'),
            'state' => $state,
        ]);

        return redirect("https://www.tiktok.com/v2/auth/authorize/?{$query}");
    }

    private function handleTiktokCallback(Request $request): RedirectResponse
    {
        if ($request->get('state') !== session('tiktok_login_oauth_state')) {
            return redirect()->route('login')->with('error', 'Could not sign in with TikTok. Please try again.');
        }
        session()->forget('tiktok_login_oauth_state');

        $token = Http::asForm()->post('https://open.tiktokapis.com/v2/oauth/token/', [
            'client_key' => Setting::get('tiktok_login_client_id'),
            'client_secret' => Setting::get('tiktok_login_client_secret'),
            'code' => $request->get('code'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => route('social.callback', 'tiktok'),
        ]);

        if ($token->failed() || !$token->json('open_id')) {
            return redirect()->route('login')->with('error', 'Could not sign in with TikTok. Please try again.');
        }

        $openId = $token->json('open_id');
        $accessToken = $token->json('access_token');

        $info = Http::withToken($accessToken)->get('https://open.tiktokapis.com/v2/user/info/', [
            'fields' => 'display_name',
        ])->json('data.user');

        // TikTok's API never returns an email address — createMinimalUser()
        // and the account-linking logic in loginOrRegister() both already
        // tolerate a null email (same as a member who signs up via WhatsApp
        // OTP with no email on file), so this is a supported path, not a
        // gap being papered over.
        return $this->loginOrRegister('tiktok', $openId, $info['display_name'] ?? 'Sallaamti User', null);
    }

    // Shared by every provider (Socialite-based or manual) — find an
    // existing account by provider+id, fall back to matching by email if
    // one was provided, otherwise create a new minimal account, then log in.
    private function loginOrRegister(string $provider, string $providerId, string $name, ?string $email): RedirectResponse
    {
        $user = User::where('provider', $provider)->where('provider_id', $providerId)->first();

        if (!$user && $email) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update(['provider' => $provider, 'provider_id' => $providerId]);
            }
        }

        if (!$user) {
            $user = $this->createMinimalUser($name, $email, null, provider: $provider);
            $user->update(['provider_id' => $providerId]);
        }

        if (!$user->email_verified_at && $email) {
            $user->email_verified_at = now();
            $user->save();
        }

        if ($user->isDeactivated()) {
            $user->reactivate();
            session()->flash('status', 'Welcome back! Your account has been reactivated.');
        }

        Auth::login($user);

        session()->flash('conversion_event', 'user_login');

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
