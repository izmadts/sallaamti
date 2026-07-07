<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-card" style="max-width: 560px">

            <div class="text-center mb-8">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-14 w-auto mx-auto" />
                </a>
                <h2 class="text-2xl font-extrabold text-gray-800 mt-4">Join Sallaamti</h2>
                <p class="text-gray-500 text-sm mt-1">Create your free account in under 2 minutes</p>
            </div>

            {{-- Trust bar --}}
            <div class="flex justify-center gap-4 flex-wrap mb-6">
                @foreach (['Free to Join', 'Quran Courses', 'Nikah Platform', 'Live Classes'] as $t)
                <span class="text-xs font-medium flex items-center gap-1" style="color: var(--teal)">
                    <i class="fa fa-check-circle" style="color: var(--gold)"></i> {{ $t }}
                </span>
                @endforeach
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div>
                    <label class="auth-label">Full Name</label>
                    <div class="auth-input-wrap">
                        <i class="fa fa-user auth-icon"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="auth-input @error('name') auth-input-error @enderror"
                            placeholder="Your full name">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                {{-- Email --}}
                <div>
                    <label class="auth-label">Email Address</label>
                    <div class="auth-input-wrap">
                        <i class="fa fa-envelope auth-icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="auth-input @error('email') auth-input-error @enderror"
                            placeholder="your@email.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                {{-- Phone + Gender --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="auth-label">Phone Number</label>
                        <div class="auth-input-wrap">
                            <i class="fa fa-phone auth-icon"></i>
                            <input type="text" name="phone" value="{{ old('phone') }}" required
                                class="auth-input @error('phone') auth-input-error @enderror"
                                placeholder="03XXXXXXXXX">
                        </div>
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>
                    <div>
                        <label class="auth-label">Gender</label>
                        <div class="auth-input-wrap">
                            <i class="fa fa-venus-mars auth-icon"></i>
                            <select name="gender" required class="auth-input @error('gender') auth-input-error @enderror">
                                <option value="">Select</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('gender')" class="mt-1" />
                    </div>
                </div>

                {{-- City --}}
                <div>
                    <label class="auth-label">City <span class="text-gray-400 font-normal">(optional)</span></label>
                    <div class="auth-input-wrap">
                        <i class="fa fa-map-marker-alt auth-icon"></i>
                        <input type="text" name="city" value="{{ old('city') }}"
                            class="auth-input" placeholder="Karachi, Lahore, London...">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="auth-label">Password</label>
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

                {{-- Confirm Password --}}
                <div>
                    <label class="auth-label">Confirm Password</label>
                    <div class="auth-input-wrap" x-data="{ show: false }">
                        <i class="fa fa-lock auth-icon"></i>
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                            class="auth-input @error('password_confirmation') auth-input-error @enderror"
                            placeholder="Repeat your password">
                        <button type="button" @click="show = !show" class="auth-eye-btn">
                            <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                {{-- Terms --}}
                <div class="flex items-start gap-2">
                    <input type="checkbox" id="terms" required class="auth-checkbox mt-0.5 flex-shrink-0">
                    <label for="terms" class="text-xs text-gray-500 leading-relaxed">
                        I agree to Sallaamti's
                        <a href="#" style="color: var(--teal)">Terms of Service</a>
                        and
                        <a href="#" style="color: var(--teal)">Privacy Policy</a>.
                        I understand this is an Islamic platform built on trust and values.
                    </label>
                </div>

                <button type="submit" class="btn-base btn-teal w-full py-3 text-base font-semibold">
                    Create My Account <i class="fa fa-arrow-right ml-2"></i>
                </button>

                <div class="auth-divider"><span>or</span></div>

                <p class="text-center text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold" style="color: var(--teal)">Sign In</a>
                </p>
            </form>

        </div>
    </div>
</x-guest-layout>