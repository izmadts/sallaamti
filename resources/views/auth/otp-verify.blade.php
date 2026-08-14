<x-auth-layout>
    <div class="auth-wrapper">
        <div class="auth-card">

            <div class="text-center mb-8">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-14 w-auto mx-auto" />
                </a>
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mx-auto mt-4 mb-3" style="background: var(--teal-light)">✉️</div>
                <h2 class="text-2xl font-extrabold text-gray-800">{{ __('db.Enter Your Code') }}</h2>
                <p class="text-gray-500 text-sm mt-2 max-w-xs mx-auto">
                    {{ __('db.We sent a 6-digit code to :email', ['email' => $pending['email']]) }}
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('otp.verify.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="auth-label">{{ __('db.Verification Code') }}</label>
                    <div class="auth-input-wrap">
                        <i class="fa fa-key auth-icon"></i>
                        <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus
                            class="auth-input @error('code') auth-input-error @enderror"
                            placeholder="123456">
                    </div>
                    <x-input-error :messages="$errors->get('code')" class="mt-1" />
                </div>

                <button type="submit" class="btn-base btn-teal w-full py-3 text-base font-semibold">
                    {{ __('db.Verify & Continue') }} <i class="fa fa-check ml-2"></i>
                </button>

                <div class="text-center">
                    <a href="{{ route('otp.request') }}" class="text-sm font-medium" style="color: var(--teal)">
                        {{ __('db.Wrong number? Start over') }}
                    </a>
                </div>
            </form>

        </div>
    </div>
</x-auth-layout>
