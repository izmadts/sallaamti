{{-- resources/views/components/matchmaker-layout.blade.php --}}
{{-- The matchmaker's own themed workspace shell — deliberately NOT the
     admin panel's teal-900 look. Deep plum/gold, its own identity, per the
     explicit "own color theme not as admin dashboard" requirement. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Match Maker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --mm-plum: #6B2447;
            --mm-plum-dark: #4A1830;
            --mm-plum-light: #8C3564;
            --mm-gold: #C9A227;
            --mm-gold-light: #E4C55E;
        }

        .mm-gradient {
            background: linear-gradient(135deg, var(--mm-plum) 0%, var(--mm-plum-dark) 100%);
        }
    </style>
</head>

<body class="bg-[#FBF6F9] font-sans antialiased" x-data="{ sidebarOpen: false }">

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

    <div class="flex h-screen overflow-hidden">

        {{-- ===== SIDEBAR ===== --}}
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 text-white flex flex-col transform transition-transform duration-200 ease-in-out
                   lg:static lg:translate-x-0"
            style="background: var(--mm-plum-dark);"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b" style="border-color: var(--mm-plum);">
                @if (file_exists(public_path('images/sallaamti-logo.png')))
                <img src="{{ asset('images/sallaamti-logo.png') }}" class="w-8 h-8 object-contain rounded-full bg-white/10 p-0.5">
                @else
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold" style="background: var(--mm-gold); color: var(--mm-plum-dark);">S</div>
                @endif
                <div>
                    <p class="font-bold text-white leading-none">Sallaamti</p>
                    <p class="text-xs" style="color: var(--mm-gold-light);">Match Maker Desk</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5 text-sm">

                <a href="{{ route('dashboard.matchmaker') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('dashboard.matchmaker') ? 'text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                    @if(request()->routeIs('dashboard.matchmaker')) style="background: var(--mm-plum);" @endif>
                    <span class="text-base">🏠</span> Dashboard
                </a>

                <p class="text-xs uppercase tracking-widest px-3 pt-4 pb-1" style="color: var(--mm-gold-light);">Clients</p>

                <a href="{{ route('matchmaker.clients.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.clients.*') ? 'text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                    @if(request()->routeIs('matchmaker.clients.*')) style="background: var(--mm-plum);" @endif>
                    <span class="text-base">🗂️</span> My Clients
                </a>
                <a href="{{ route('matchmaker.clients.create') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.clients.create') ? 'text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                    @if(request()->routeIs('matchmaker.clients.create')) style="background: var(--mm-plum);" @endif>
                    <span class="text-base">➕</span> Add Client
                </a>

                <p class="text-xs uppercase tracking-widest px-3 pt-4 pb-1" style="color: var(--mm-gold-light);">Nikah Profiles</p>

                <a href="{{ route('matchmaker.nikah.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.nikah.index') || request()->routeIs('matchmaker.nikah.show') ? 'text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                    @if(request()->routeIs('matchmaker.nikah.index') || request()->routeIs('matchmaker.nikah.show')) style="background: var(--mm-plum);" @endif>
                    <span class="text-base">🔎</span> Browse Profiles
                </a>
                <a href="{{ route('matchmaker.nikah.requests') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.nikah.requests') ? 'text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                    @if(request()->routeIs('matchmaker.nikah.requests')) style="background: var(--mm-plum);" @endif>
                    <span class="text-base">📨</span> My Contact Requests
                </a>
            </nav>

            {{-- Bottom: Back to site --}}
            <div class="px-3 py-4 border-t space-y-1" style="border-color: var(--mm-plum);">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/10 hover:text-white text-sm transition">
                    <span class="text-base">↩️</span> Back to Site
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/10 hover:text-white text-sm transition text-left">
                        <span class="text-base">🚪</span> Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top bar --}}
            <header class="mm-gradient px-4 py-3 flex items-center justify-between flex-shrink-0 shadow-md">
                <div class="flex items-center gap-3">
                    {{-- Mobile menu toggle --}}
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Page title slot --}}
                    @isset($header)
                    <div class="text-white [&_h2]:text-white [&_p]:text-white/80">{{ $header }}</div>
                    @endisset
                </div>

                {{-- Matchmaker info --}}
                <div class="flex items-center gap-3">
                    <x-language-switcher />
                    <span class="text-xs hidden sm:block" style="color: var(--mm-gold-light);">{{ now()->format('d M Y') }}</span>
                    <img src="{{ Auth::user()->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover ring-2" style="--tw-ring-color: var(--mm-gold);">
                    <span class="text-sm text-white hidden sm:block">{{ Auth::user()->name }}</span>
                </div>
            </header>

            {{-- Scrollable page content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @if (isset($errors) && $errors->any())
                <div class="mb-6">
                    <x-alert type="error">
                        <strong>Please fix the following before saving:</strong>
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
