<x-auth-layout>
    <div class="auth-wrapper">
        <div class="auth-card" style="max-width: 560px" x-data="{ tab: 'register' }">

            <div class="text-center mb-6">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-14 w-auto mx-auto" />
                </a>
            </div>

            @php
                $moduleParam = old('module', request('module'));
                $moduleLabels = [
                    'nikah' => ['💍', __('db.Nikah')], 'quran' => ['📖', __('db.Quran Courses')],
                    'quran_live' => ['🎥', __('db.Live Classes')], 'skills' => ['💻', __('db.Digital Skills')],
                    'counseling' => ['💑', __('db.Family Support')], 'donation' => ['💝', __('db.Donate')],
                    'volunteer' => ['🤝', __('db.Volunteer')], 'wall' => ['🤲', __('db.Sallaamti Wall')],
                ];
            @endphp
            @if ($moduleParam && isset($moduleLabels[$moduleParam]))
            <div class="flex items-center gap-2 justify-center text-sm font-medium rounded-lg py-2.5 px-4 mb-6" style="background: var(--teal-light); color: var(--teal)">
                <span class="text-lg">{{ $moduleLabels[$moduleParam][0] }}</span>
                {{ __('db.Great choice! Create your free account to start with :module.', ['module' => $moduleLabels[$moduleParam][1]]) }}
            </div>
            @endif

            {{-- Tabs --}}
            <div class="flex rounded-xl p-1 mb-6" style="background: #f1f5f5">
                <button type="button" @click="tab = 'login'"
                    :class="tab === 'login' ? 'bg-white shadow-sm' : 'text-gray-500'"
                    class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition" :style="tab === 'login' ? 'color: var(--teal)' : ''">
                    {{ __('db.Sign In') }}
                </button>
                <button type="button" @click="tab = 'register'"
                    :class="tab === 'register' ? 'bg-white shadow-sm' : 'text-gray-500'"
                    class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition" :style="tab === 'register' ? 'color: var(--teal)' : ''">
                    {{ __('db.Create Account') }}
                </button>
            </div>

            <div x-show="tab === 'login'" x-cloak>
                <div class="text-center mb-5">
                    <h2 class="text-lg font-bold text-gray-800">{{ __('db.Welcome Back') }}</h2>
                    <p class="text-gray-500 text-xs mt-1">{{ __('db.Sign in to continue') }}</p>
                </div>
                @include('auth.partials.login-form')
            </div>

            <div x-show="tab === 'register'">
                <div class="text-center mb-5">
                    <h2 class="text-2xl font-extrabold text-gray-800">{{ __('db.Join Sallaamti') }}</h2>
                    <p class="text-gray-500 text-sm mt-1">{{ __('db.Create your free account in under 2 minutes') }}</p>
                </div>

                {{-- Trust bar --}}
                <div class="flex justify-center gap-4 flex-wrap mb-6">
                    @foreach (['Free to Join', 'Quran Courses', 'Nikah Platform', 'Live Classes'] as $t)
                    <span class="text-xs font-medium flex items-center gap-1" style="color: var(--teal)">
                        <i class="fa fa-check-circle" style="color: var(--gold)"></i> {{ __("db.$t") }}
                    </span>
                    @endforeach
                </div>

                @include('auth.partials.register-form')
            </div>

        </div>
    </div>
</x-auth-layout>
