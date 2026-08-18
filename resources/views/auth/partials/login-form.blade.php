{{-- Shared login form — included by both auth/login.blade.php and the
     split-screen homepage (resources/views/index.blade.php), so the two
     never drift apart. --}}
<x-auth-session-status class="mb-4" :status="session('status')" />

<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    <div>
        <label class="auth-label">{{ __('db.Email Address') }}</label>
        <div class="auth-input-wrap">
            <i class="fa fa-envelope auth-icon"></i>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="auth-input @error('email') auth-input-error @enderror"
                placeholder="your@email.com">
        </div>
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>

    <div>
        <div class="flex justify-between items-center mb-1">
            <label class="auth-label mb-0">{{ __('db.Password') }}</label>
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-xs font-medium" style="color: var(--teal)">{{ __('db.Forgot password?') }}</a>
            @endif
        </div>
        <div class="auth-input-wrap" x-data="{ show: false }">
            <i class="fa fa-lock auth-icon"></i>
            <input :type="show ? 'text' : 'password'" name="password" required
                class="auth-input @error('password') auth-input-error @enderror"
                placeholder="••••••••">
            <button type="button" @click="show = !show" class="auth-eye-btn">
                <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
            </button>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="remember" id="remember" class="auth-checkbox">
        <label for="remember" class="text-sm text-gray-600">{{ __('db.Remember me') }}</label>
    </div>

    <button type="submit" class="btn-base btn-teal w-full py-3 text-base font-semibold">
        {{ __('db.Sign In') }} <i class="fa fa-arrow-right ml-2"></i>
    </button>

    @php
        $googleReady = \App\Http\Controllers\Auth\SocialAuthController::isEnabled('google');
        $facebookReady = \App\Http\Controllers\Auth\SocialAuthController::isEnabled('facebook');
    @endphp
    @if ($googleReady || $facebookReady)
    <div class="auth-divider"><span>{{ __('db.or continue with') }}</span></div>

    <div class="space-y-3">
        @if ($googleReady)
        <a href="{{ route('social.redirect', 'google') }}"
            class="flex items-center justify-center gap-2.5 border-2 border-gray-200 rounded-lg py-2.5 text-sm font-semibold text-gray-700 bg-white hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 transition-all duration-150">
            <i class="fab fa-google text-red-500"></i> {{ __('db.Sign in with Google') }}
        </a>
        @endif
        @if ($facebookReady)
        <a href="{{ route('social.redirect', 'facebook') }}"
            class="flex items-center justify-center gap-2.5 border-2 border-[#1877F2] rounded-lg py-2.5 text-sm font-semibold text-white bg-[#1877F2] hover:bg-[#1465d1] hover:border-[#1465d1] hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 transition-all duration-150">
            <i class="fab fa-facebook"></i> {{ __('db.Sign in with Facebook') }}
        </a>
        @endif
    </div>
    @endif

    {{-- WhatsApp/OTP login hidden until that flow is working — route and
         controller left intact, just not linked to from here. --}}

    <p class="text-center text-sm text-gray-600">
        {{ __("db.Don't have an account?") }}
        <a href="{{ route('register') }}" class="font-semibold" style="color: var(--teal)">{{ __('db.Register Free') }}</a>
    </p>
</form>
