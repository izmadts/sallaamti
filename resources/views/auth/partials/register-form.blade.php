{{-- Shared register form — included by both auth/register.blade.php and the
     split-screen homepage (resources/views/index.blade.php), so the two
     never drift apart. --}}
<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="module" value="{{ old('module', request('module')) }}">
    @php
        $moduleParam = old('module', request('module'));
        $initNikah = old('nikah_module_enabled') !== null ? old('nikah_module_enabled') == '1' : ($moduleParam ? $moduleParam === 'nikah' : true);
        $initQuran = old('quran_module_enabled') !== null ? old('quran_module_enabled') == '1' : ($moduleParam ? in_array($moduleParam, ['quran', 'quran_live']) : false);
        $initCounseling = old('counseling_module_enabled') !== null ? old('counseling_module_enabled') == '1' : $moduleParam === 'counseling';
        $initSkills = old('skills_module_enabled') !== null ? old('skills_module_enabled') == '1' : $moduleParam === 'skills';
    @endphp

    {{-- Name --}}
    <div>
        <label class="auth-label">{{ __('db.Full Name') }}</label>
        <div class="auth-input-wrap">
            <i class="fa fa-user auth-icon"></i>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="auth-input @error('name') auth-input-error @enderror"
                placeholder="{{ __('db.Your full name') }}">
        </div>
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    {{-- Email or WhatsApp Number --}}
    <div>
        <label class="auth-label">{{ __('db.Email or WhatsApp Number') }}</label>
        <div class="auth-input-wrap">
            <i class="fa fa-at auth-icon"></i>
            <input type="text" name="identifier" value="{{ old('identifier') }}" required
                class="auth-input @error('identifier') auth-input-error @enderror"
                placeholder="{{ __('db.your@email.com or 03XXXXXXXXX') }}">
        </div>
        <x-input-error :messages="$errors->get('identifier')" class="mt-1" />
    </div>

    {{-- Password --}}
    <div>
        <label class="auth-label">{{ __('db.Password') }}</label>
        <div class="auth-input-wrap" x-data="{ show: false }">
            <i class="fa fa-lock auth-icon"></i>
            <input :type="show ? 'text' : 'password'" name="password" required
                class="auth-input @error('password') auth-input-error @enderror"
                placeholder="{{ __('db.Min. 8 characters') }}">
            <button type="button" @click="show = !show" class="auth-eye-btn">
                <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
            </button>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
    </div>

    {{-- Confirm Password --}}
    <div>
        <label class="auth-label">{{ __('db.Confirm Password') }}</label>
        <div class="auth-input-wrap" x-data="{ show: false }">
            <i class="fa fa-lock auth-icon"></i>
            <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                class="auth-input @error('password_confirmation') auth-input-error @enderror"
                placeholder="{{ __('db.Repeat your password') }}">
            <button type="button" @click="show = !show" class="auth-eye-btn">
                <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
            </button>
        </div>
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
    </div>

    {{-- Interests — real, functional checkboxes (not just decoration). Drives
         what shows up in this member's menu/dashboard after signup AND which
         page they land on right after registering — changeable anytime from
         the profile page. --}}
    <div x-data="{ nikah: {{ $initNikah ? 'true' : 'false' }}, quran: {{ $initQuran ? 'true' : 'false' }}, counseling: {{ $initCounseling ? 'true' : 'false' }}, skills: {{ $initSkills ? 'true' : 'false' }} }">
        <label class="auth-label">
            {{ __('db.What brings you to Sallaamti?') }}
            <span class="text-gray-400 block text-xs mt-0.5 font-normal">{{ __('db.Pick one or more — we\'ll take you straight there after you sign up.') }}</span>
        </label>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2">
            <label class="relative flex flex-col items-center gap-1 py-3 px-1 rounded-xl border-2 cursor-pointer transition text-center"
                :class="nikah ? 'border-teal-500' : 'border-gray-200 hover:border-gray-300'"
                :style="nikah ? 'background: #f0fdfa' : ''">
                <input type="checkbox" name="nikah_module_enabled" value="1" x-model="nikah" class="sr-only">
                <span class="text-2xl" :class="nikah ? 'opacity-100' : 'opacity-40'">💍</span>
                <span class="text-xs font-semibold" style="color: var(--teal)">{{ __('db.Nikah') }}</span>
                <span class="text-[10px] text-gray-500 leading-tight">{{ __('db.Find a match') }}</span>
                <span x-show="nikah" x-cloak class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full text-white flex items-center justify-center text-[10px] shadow" style="background: var(--teal)">✓</span>
            </label>
            <label class="relative flex flex-col items-center gap-1 py-3 px-1 rounded-xl border-2 cursor-pointer transition text-center"
                :class="quran ? 'border-teal-500' : 'border-gray-200 hover:border-gray-300'"
                :style="quran ? 'background: #f0fdfa' : ''">
                <input type="checkbox" name="quran_module_enabled" value="1" x-model="quran" class="sr-only">
                <span class="text-2xl" :class="quran ? 'opacity-100' : 'opacity-40'">📖</span>
                <span class="text-xs font-semibold" style="color: var(--teal)">{{ __('db.Quran') }}</span>
                <span class="text-[10px] text-gray-500 leading-tight">{{ __('db.Courses & classes') }}</span>
                <span x-show="quran" x-cloak class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full text-white flex items-center justify-center text-[10px] shadow" style="background: var(--teal)">✓</span>
            </label>
            <label class="relative flex flex-col items-center gap-1 py-3 px-1 rounded-xl border-2 cursor-pointer transition text-center"
                :class="counseling ? 'border-teal-500' : 'border-gray-200 hover:border-gray-300'"
                :style="counseling ? 'background: #f0fdfa' : ''">
                <input type="checkbox" name="counseling_module_enabled" value="1" x-model="counseling" class="sr-only">
                <span class="text-2xl" :class="counseling ? 'opacity-100' : 'opacity-40'">🤝</span>
                <span class="text-xs font-semibold" style="color: var(--teal)">{{ __('db.Support') }}</span>
                <span class="text-[10px] text-gray-500 leading-tight">{{ __('db.Family counseling') }}</span>
                <span x-show="counseling" x-cloak class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full text-white flex items-center justify-center text-[10px] shadow" style="background: var(--teal)">✓</span>
            </label>
            <label class="relative flex flex-col items-center gap-1 py-3 px-1 rounded-xl border-2 cursor-pointer transition text-center"
                :class="skills ? 'border-teal-500' : 'border-gray-200 hover:border-gray-300'"
                :style="skills ? 'background: #f0fdfa' : ''">
                <input type="checkbox" name="skills_module_enabled" value="1" x-model="skills" class="sr-only">
                <span class="text-2xl" :class="skills ? 'opacity-100' : 'opacity-40'">💻</span>
                <span class="text-xs font-semibold" style="color: var(--teal)">{{ __('db.Skills') }}</span>
                <span class="text-[10px] text-gray-500 leading-tight">{{ __('db.Digital training') }}</span>
                <span x-show="skills" x-cloak class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full text-white flex items-center justify-center text-[10px] shadow" style="background: var(--teal)">✓</span>
            </label>
        </div>
        <p class="text-[11px] text-gray-400 mt-2 leading-relaxed">
            💡 {{ __('db.Tap an icon anytime to turn that module on or off — nothing is ever deleted, and you can always change this again later from your profile or the topbar.') }}
        </p>
    </div>

    {{-- Terms --}}
    <div class="flex items-start gap-2">
        <input type="checkbox" id="terms" required class="auth-checkbox mt-0.5 flex-shrink-0">
        <label for="terms" class="text-xs text-gray-500 leading-relaxed">
            {{ __("db.I agree to Sallaamti's") }}
            <a href="{{ route('terms-of-service') }}" target="_blank" style="color: var(--teal)">{{ __('db.Terms of Service') }}</a>
            {{ __('db.and') }}
            <a href="{{ route('privacy-policy') }}" target="_blank" style="color: var(--teal)">{{ __('db.Privacy Policy') }}</a>.
            {{ __('db.I understand this is an Islamic platform built on trust and values.') }}
        </label>
    </div>

    <button type="submit" class="btn-base btn-teal w-full py-3 text-base font-semibold">
        {{ __('db.Create My Account') }} <i class="fa fa-arrow-right ml-2"></i>
    </button>

    @php
        $googleReady = \App\Http\Controllers\Auth\SocialAuthController::isEnabled('google');
        $facebookReady = \App\Http\Controllers\Auth\SocialAuthController::isEnabled('facebook');
        $tiktokReady = \App\Http\Controllers\Auth\SocialAuthController::isEnabled('tiktok');
    @endphp
    @if ($googleReady || $facebookReady || $tiktokReady)
    <div class="auth-divider"><span>{{ __('db.or continue with') }}</span></div>

    <div class="space-y-3">
        @if ($googleReady)
        <a href="{{ route('social.redirect', 'google') }}"
            class="flex items-center justify-center gap-2.5 border-2 border-gray-200 rounded-lg py-2.5 text-sm font-semibold text-gray-700 bg-white hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 transition-all duration-150">
            <i class="fab fa-google text-red-500"></i> {{ __('db.Sign up with Google') }}
        </a>
        @endif
        @if ($facebookReady)
        <a href="{{ route('social.redirect', 'facebook') }}"
            class="flex items-center justify-center gap-2.5 border-2 border-[#1877F2] rounded-lg py-2.5 text-sm font-semibold text-white bg-[#1877F2] hover:bg-[#1465d1] hover:border-[#1465d1] hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 transition-all duration-150">
            <i class="fab fa-facebook"></i> {{ __('db.Sign up with Facebook') }}
        </a>
        @endif
        @if ($tiktokReady)
        <a href="{{ route('social.redirect', 'tiktok') }}"
            class="flex items-center justify-center gap-2.5 border-2 border-black rounded-lg py-2.5 text-sm font-semibold text-white bg-black hover:bg-gray-900 hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 transition-all duration-150">
            <i class="fab fa-tiktok"></i> {{ __('db.Sign up with TikTok') }}
        </a>
        @endif
    </div>
    @endif

    <p class="text-center text-sm text-gray-600">
        {{ __('db.Already have an account?') }}
        <a href="{{ route('login') }}" class="font-semibold" style="color: var(--teal)">{{ __('db.Sign In') }}</a>
    </p>
</form>
