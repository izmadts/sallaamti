<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Http\Controllers\Concerns\TracksReferrals;
use App\Mail\SocialProviderLinked;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

// Shared by the web SocialAuthController (session login + redirect) and the
// mobile Api\V1\AuthController (Sanctum token + JSON) so the account-linking
// and "someone linked a new provider to your email" notification logic —
// which has real security weight — lives in exactly one place rather than
// being copied between the two.
trait ResolvesSocialLogin
{
    use RegistersMinimalUsers, TracksReferrals;

    // Set by resolveSocialUser() on every call — callers that want to show
    // a "welcome back, your account was reactivated" message check this
    // right after, since by the time the user is returned it's already
    // been reactivated in-place.
    protected bool $socialLoginWasReactivated = false;

    protected function resolveSocialUser(string $provider, string $providerId, string $name, ?string $email): User
    {
        $this->socialLoginWasReactivated = false;

        $user = User::where('provider', $provider)->where('provider_id', $providerId)->first();

        if (!$user && $email) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update(['provider' => $provider, 'provider_id' => $providerId]);

                if ($user->email) {
                    try {
                        Mail::to($user->email)->send(new SocialProviderLinked($user, $provider));
                    } catch (\Throwable $e) {
                        \Log::error('SocialProviderLinked email failed: ' . $e->getMessage());
                    }
                }
            }
        }

        if (!$user) {
            $user = $this->createMinimalUser($name, $email, null, provider: $provider);
            $user->update(['provider_id' => $providerId]);
            $this->attributeReferral($user);
        }

        if (!$user->email_verified_at && $email) {
            $user->email_verified_at = now();
            $user->save();
        }

        if ($user->isDeactivated()) {
            $user->reactivate();
            $this->socialLoginWasReactivated = true;
        }

        return $user;
    }
}
