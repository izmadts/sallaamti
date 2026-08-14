<x-auth-layout>
    <div class="auth-wrapper">
        <div class="auth-card">

            <div class="text-center mb-8">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-14 w-auto mx-auto" />
                </a>
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mx-auto mt-4 mb-3" style="background: var(--teal-light)">📱</div>
                <h2 class="text-2xl font-extrabold text-gray-800">{{ __('db.Continue with WhatsApp Number') }}</h2>
                <p class="text-gray-500 text-sm mt-2 max-w-xs mx-auto">{{ __("db.We'll email you a 6-digit code to verify it's you — free, no paid SMS.") }}</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('otp.request.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="auth-label">{{ __('db.WhatsApp Number') }}</label>
                    <div class="auth-input-wrap">
                        <i class="fa fa-phone auth-icon"></i>
                        <input type="text" name="phone" value="{{ old('phone') }}" required autofocus
                            class="auth-input @error('phone') auth-input-error @enderror"
                            placeholder="03XXXXXXXXX">
                    </div>
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <div>
                    <label class="auth-label">{{ __('db.Full Name') }} <span class="text-gray-400 font-normal">({{ __('db.only needed if this is your first time') }})</span></label>
                    <div class="auth-input-wrap">
                        <i class="fa fa-user auth-icon"></i>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="auth-input @error('name') auth-input-error @enderror"
                            placeholder="{{ __('db.Your full name') }}">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div>
                    <label class="auth-label">{{ __('db.Email Address') }} <span class="text-gray-400 font-normal">({{ __('db.to receive your code') }})</span></label>
                    <div class="auth-input-wrap">
                        <i class="fa fa-envelope auth-icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="auth-input @error('email') auth-input-error @enderror"
                            placeholder="your@email.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <button type="submit" class="btn-base btn-teal w-full py-3 text-base font-semibold">
                    {{ __('db.Send Code') }} <i class="fa fa-paper-plane ml-2"></i>
                </button>

                <div class="auth-divider"><span>{{ __('db.or') }}</span></div>

                <p class="text-center text-sm text-gray-600">
                    <a href="{{ route('login') }}" class="font-semibold" style="color: var(--teal)">{{ __('db.Sign in with password instead') }}</a>
                </p>
            </form>

        </div>
    </div>
</x-auth-layout>
