{{-- resources/views/components/matchmaker-layout.blade.php --}}
{{-- Dark pink sidebar, distinct from admin's teal-900 — safe to do now that
     every page the matchmaker workflow hands off into (the walk-in Nikah
     wizard, in particular) dynamically renders THIS layout or x-admin-layout
     based on which route family served the request, rather than always
     rendering admin's. Before that fix, a matchmaker mid-task would get
     bounced into an admin-styled page regardless of this component's own
     colors — a distinct palette just meant a jarring flip with no benefit.
     See Admin\NikahProfileWizardController::routeNamePrefix(). --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}" translate="no">

<head>
    <meta charset="utf-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — {{ __('db.Nikah Counselor') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            /* Kept the --mm-* names so every matchmaker view that already
               references them (dashboard, clients index/create/show) picks
               up this palette automatically without needing every inline
               style="" touched individually. */
            --mm-plum: #be185d;
            /* Tailwind pink-700 — active nav state, primary buttons */
            --mm-plum-dark: #831843;
            /* Tailwind pink-900 — sidebar background */
            --mm-plum-light: #db2777;
            /* Tailwind pink-600 */
            --mm-gold: #ec4899;
            /* Tailwind pink-500 — secondary accent buttons */
            --mm-gold-light: #f9a8d4;
            /* Tailwind pink-300 */
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

    <div class="flex h-screen overflow-hidden">

        {{-- ===== SIDEBAR ===== --}}
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-pink-900 text-white flex flex-col transform transition-transform duration-200 ease-in-out
                   lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-pink-700">
                @if (file_exists(public_path('images/sallaamti-logo.png')))
                <img src="{{ asset('images/sallaamti-logo.png') }}" class="w-8 h-8 object-contain">
                @else
                <div class="w-8 h-8 bg-pink-600 rounded-full flex items-center justify-center text-xs font-bold">S</div>
                @endif
                <div>
                    <p class="font-bold text-white leading-none">{{ __('db.Sallaamti') }}</p>
                    <p class="text-pink-300 text-xs">{{ __('db.Nikah Counselor Desk') }}</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5 text-sm">

                <a href="{{ route('dashboard.matchmaker') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('dashboard.matchmaker') ? 'bg-pink-700 text-white' : 'text-pink-100 hover:bg-pink-800' }}">
                    <span class="text-base">🏠</span> {{ __('db.Dashboard') }}
                </a>

                <p class="text-pink-400 text-xs uppercase tracking-widest px-3 pt-4 pb-1">{{ __('db.Clients') }}</p>

                <a href="{{ route('matchmaker.clients.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.clients.*') ? 'bg-pink-700 text-white' : 'text-pink-100 hover:bg-pink-800' }}">
                    <span class="text-base">🗂️</span> {{ __('db.My Clients') }}
                </a>
                <a href="{{ route('matchmaker.clients.create') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.clients.create') ? 'bg-pink-700 text-white' : 'text-pink-100 hover:bg-pink-800' }}">
                    <span class="text-base">➕</span> {{ __('db.Add Client') }}
                </a>

                <p class="text-pink-400 text-xs uppercase tracking-widest px-3 pt-4 pb-1">{{ __('db.Nikah Profiles') }}</p>

                <a href="{{ route('matchmaker.nikah.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.nikah.index') || request()->routeIs('matchmaker.nikah.show') ? 'bg-pink-700 text-white' : 'text-pink-100 hover:bg-pink-800' }}">
                    <span class="text-base">🔎</span> {{ __('db.Browse Profiles') }}
                </a>
                <a href="{{ route('matchmaker.nikah.requests') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.nikah.requests') ? 'bg-pink-700 text-white' : 'text-pink-100 hover:bg-pink-800' }}">
                    <span class="text-base">📨</span> {{ __('db.My Contact Requests') }}
                </a>
                <a href="{{ route('nikah.packages') }}" target="_blank"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-pink-100 hover:bg-pink-800">
                    <span class="text-base">💳</span> {{ __('db.Nikah Packages') }}
                </a>
                <a href="{{ route('matchmaker.commissions.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.commissions*') ? 'bg-pink-700 text-white' : 'text-pink-100 hover:bg-pink-800' }}">
                    <span class="text-base">💰</span> {{ __('db.My Commissions') }}
                </a>
                <a href="{{ route('matchmaker.performance.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.performance*') ? 'bg-pink-700 text-white' : 'text-pink-100 hover:bg-pink-800' }}">
                    <span class="text-base">📊</span> {{ __('db.My Performance') }}
                </a>

                <p class="text-pink-400 text-xs uppercase tracking-widest px-3 pt-4 pb-1">{{ __('db.Help') }}</p>
                <a href="{{ route('guide.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('guide.*') ? 'bg-pink-700 text-white' : 'text-pink-100 hover:bg-pink-800' }}">
                    <span class="text-base">📘</span> {{ __('db.Nikah Counselor Guide') }}
                </a>
            </nav>

            {{-- Bottom: Back to site --}}
            <div class="px-3 py-4 border-t border-pink-700 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-pink-300 hover:bg-pink-800 text-sm transition">
                    <span class="text-base">↩️</span> {{ __('db.Back to Site') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-pink-300 hover:bg-pink-800 text-sm transition text-left">
                        <span class="text-base">🚪</span> {{ __('db.Logout') }}
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top bar --}}
            <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    {{-- Mobile menu toggle --}}
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Page title slot --}}
                    @isset($header)
                    <div>{{ $header }}</div>
                    @endisset
                </div>

                {{-- Matchmaker info --}}
                <div class="flex items-center gap-3">
                    <x-language-switcher />
                    <span class="text-xs text-gray-400 hidden sm:block">{{ now()->format('d M Y') }}</span>
                    @php
                        $myApplication = \App\Models\MatchmakerApplication::where('user_id', Auth::id())->where('status', 'certified')->first();
                        $tierColors = ['nikah_counselor' => '#0d6b6b', 'certified_nikah_counselor' => '#1a6fb8', 'senior_nikah_counselor' => '#b8962e', 'regional_nikah_coordinator' => '#7a2e8c'];
                        $tierBadges = ['nikah_counselor' => '🥉', 'certified_nikah_counselor' => '🥈', 'senior_nikah_counselor' => '🥇', 'regional_nikah_coordinator' => '⭐'];
                    @endphp
                    <a href="{{ route('matchmaker.performance.index') }}" class="relative flex-shrink-0" title="{{ $myApplication ? \App\Models\MatchmakerApplication::LEVELS[$myApplication->level] : '' }}">
                        <img src="{{ Auth::user()->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover" @if($myApplication) style="border: 2px solid {{ $tierColors[$myApplication->level] }}" @endif>
                        @if ($myApplication)
                        <span class="absolute -bottom-1 -right-1 text-xs leading-none">{{ $tierBadges[$myApplication->level] }}</span>
                        @endif
                    </a>
                    <span class="text-sm text-gray-700 hidden sm:block">{{ Auth::user()->name }}</span>
                </div>
            </header>

            {{-- Scrollable page content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @if (isset($errors) && $errors->any())
                <div class="mb-6">
                    <x-alert type="error">
                        <strong>{{ __('db.Please fix the following before saving:') }}</strong>
                        <ul class="list-disc ms-5 mt-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                </div>
                @endif
                <x-flash-messages />
                {{ $slot }}
            </main>
        </div>
    </div>

</body>

</html>
