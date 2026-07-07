<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-card">

            {{-- Logo --}}
            <div class="text-center mb-8">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-14 w-auto mx-auto" />
                </a>
                <h2 class="text-2xl font-extrabold text-gray-800 mt-4">Welcome Back</h2>
                <p class="text-gray-500 text-sm mt-1">Sign in to your Sallaamti account</p>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="auth-label">Email Address</label>
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
                        <label class="auth-label mb-0">Password</label>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-medium" style="color: var(--teal)">Forgot password?</a>
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
                    <label for="remember" class="text-sm text-gray-600">Remember me</label>
                </div>

                <button type="submit" class="btn-base btn-teal w-full py-3 text-base font-semibold">
                    Sign In <i class="fa fa-arrow-right ml-2"></i>
                </button>

                <div class="auth-divider"><span>or</span></div>

                <p class="text-center text-sm text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold" style="color: var(--teal)">Register Free</a>
                </p>
            </form>

        </div>
    </div>
</x-guest-layout>