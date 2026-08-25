<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Support\DeviceTrust;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The 'email' field now doubles as "email or mobile number" and
     * 'password' as "password or PIN" — see authenticate() below. Kept
     * these two field names rather than renaming to identifier/credential
     * so every other reference to old('email') etc. across the app keeps
     * working untouched; only what they're allowed to contain changed.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials — email or phone
     * as the identifier, password or a set-up PIN as the credential, all
     * four combinations interchangeable. A PIN is only accepted on a
     * device this exact user has already logged into with their real
     * password (or social login) at least once — see App\Support\
     * DeviceTrust's own docblock on why.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = trim((string) $this->input('email'));
        $credential = (string) $this->input('password');

        $user = User::where('email', $identifier)->orWhere('phone', $identifier)->first();

        if ($user && $user->password && Hash::check($credential, $user->password)) {
            RateLimiter::clear($this->throttleKey());
            Auth::login($user, $this->boolean('remember'));
            DeviceTrust::trust($user, $this);

            return;
        }

        if ($user && $user->pin && preg_match('/^\d{4}$/', $credential) === 1 && Hash::check($credential, $user->pin)) {
            if (!DeviceTrust::isTrusted($user, $this)) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => 'This device hasn\'t been used with your PIN before — sign in with your password once first, then your PIN will work here too.',
                ]);
            }

            RateLimiter::clear($this->throttleKey());
            Auth::login($user, $this->boolean('remember'));

            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
