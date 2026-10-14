<x-auth-layout>
    <div class="auth-wrapper">
        <div class="auth-card">

            <div class="text-center mb-8">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-14 w-auto mx-auto" />
                </a>
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mx-auto mt-4 mb-3" style="background: var(--teal-light)">🛡️</div>
                <h2 class="text-2xl font-extrabold text-gray-800">Verify It's You</h2>
                <p class="text-gray-500 text-sm mt-2 max-w-xs mx-auto">
                    This is a Super Admin account — we emailed a 6-digit security code to confirm it's really you.
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('super-admin.2fa.verify.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="auth-label">Verification Code</label>
                    <div class="auth-input-wrap">
                        <i class="fa fa-shield-alt auth-icon"></i>
                        <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus
                            class="auth-input @error('code') auth-input-error @enderror"
                            placeholder="123456">
                    </div>
                    <x-input-error :messages="$errors->get('code')" class="mt-1" />
                </div>

                <button type="submit" class="btn-base btn-teal w-full py-3 text-base font-semibold">
                    Verify & Continue <i class="fa fa-check ml-2"></i>
                </button>
            </form>

            <form method="POST" action="{{ route('super-admin.2fa.resend') }}" class="text-center mt-4">
                @csrf
                <button type="submit" class="text-sm font-medium" style="color: var(--teal)">Resend code</button>
            </form>

        </div>
    </div>
</x-auth-layout>
