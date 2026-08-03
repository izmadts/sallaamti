<x-auth-layout>
    <div class="auth-wrapper">
        <div class="auth-card">

            <div class="text-center mb-8">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-14 w-auto mx-auto" />
                </a>
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mx-auto mt-4 mb-3" style="background: var(--teal-light)">🔐</div>
                <h2 class="text-2xl font-extrabold text-gray-800">Set New Password</h2>
                <p class="text-gray-500 text-sm mt-2">Choose a strong new password for your account.</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label class="auth-label">Email Address</label>
                    <div class="auth-input-wrap">
                        <i class="fa fa-envelope auth-icon"></i>
                        <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                            class="auth-input @error('email') auth-input-error @enderror">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div>
                    <label class="auth-label">New Password</label>
                    <div class="auth-input-wrap" x-data="{ show: false }">
                        <i class="fa fa-lock auth-icon"></i>
                        <input :type="show ? 'text' : 'password'" name="password" required
                            class="auth-input @error('password') auth-input-error @enderror"
                            placeholder="Min. 8 characters">
                        <button type="button" @click="show = !show" class="auth-eye-btn">
                            <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div>
                    <label class="auth-label">Confirm New Password</label>
                    <div class="auth-input-wrap" x-data="{ show: false }">
                        <i class="fa fa-lock auth-icon"></i>
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                            class="auth-input @error('password_confirmation') auth-input-error @enderror"
                            placeholder="Repeat new password">
                        <button type="button" @click="show = !show" class="auth-eye-btn">
                            <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <button type="submit" class="btn-base btn-teal w-full py-3 text-base font-semibold">
                    Reset Password <i class="fa fa-check ml-2"></i>
                </button>
            </form>

        </div>
    </div>
</x-auth-layout>