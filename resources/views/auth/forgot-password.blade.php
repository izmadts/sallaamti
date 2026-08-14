<x-auth-layout>
    <div class="auth-wrapper">
        <div class="auth-card">

            <div class="text-center mb-8">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-14 w-auto mx-auto" />
                </a>
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mx-auto mt-4 mb-3" style="background: var(--teal-light)">🔑</div>
                <h2 class="text-2xl font-extrabold text-gray-800">{{ __('db.Forgot Password?') }}</h2>
                <p class="text-gray-500 text-sm mt-2 max-w-xs mx-auto">{{ __("db.No worries — enter your email and we'll send you a reset link.") }}</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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

                <button type="submit" class="btn-base btn-teal w-full py-3 text-base font-semibold">
                    {{ __('db.Send Reset Link') }} <i class="fa fa-paper-plane ml-2"></i>
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm font-medium flex items-center justify-center gap-1" style="color: var(--teal)">
                        <i class="fa fa-arrow-left text-xs"></i> {{ __('db.Back to Login') }}
                    </a>
                </div>
            </form>

        </div>
    </div>
</x-auth-layout>