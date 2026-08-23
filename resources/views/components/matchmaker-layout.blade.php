{{-- resources/views/components/matchmaker-layout.blade.php --}}
{{-- Matches the admin panel's own teal-900 chrome intentionally — the
     matchmaker workflow routinely hands off into admin-styled pages (the
     walk-in Nikah wizard, admin.nikah.show), so a different palette here
     just meant a jarring color flip mid-task with no real benefit. Kept as
     its own component (distinct nav items, "Match Maker Desk" label) but
     visually it's the same shell as x-admin-layout. --}}
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
            /* Kept the --mm-* names so every matchmaker view that already
               references them (dashboard, clients index/create/show) picks
               up the admin palette automatically without needing every
               inline style="" touched individually. */
            --mm-plum: #0f766e;
            /* Tailwind teal-700 — matches admin's bg-teal-700 active nav state */
            --mm-plum-dark: #134e4a;
            /* Tailwind teal-900 — matches admin's bg-teal-900 sidebar */
            --mm-plum-light: #0d9488;
            /* Tailwind teal-600 */
            --mm-gold: #0d9488;
            /* Tailwind teal-600 — was a gold accent, now teal to match admin */
            --mm-gold-light: #2dd4bf;
            /* Tailwind teal-400 — matches admin's muted sidebar text */
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

    <div class="flex h-screen overflow-hidden">

        {{-- ===== SIDEBAR ===== --}}
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-teal-900 text-white flex flex-col transform transition-transform duration-200 ease-in-out
                   lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-teal-700">
                @if (file_exists(public_path('images/sallaamti-logo.png')))
                <img src="{{ asset('images/sallaamti-logo.png') }}" class="w-8 h-8 object-contain">
                @else
                <div class="w-8 h-8 bg-teal-600 rounded-full flex items-center justify-center text-xs font-bold">S</div>
                @endif
                <div>
                    <p class="font-bold text-white leading-none">Sallaamti</p>
                    <p class="text-teal-400 text-xs">Match Maker Desk</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5 text-sm">

                <a href="{{ route('dashboard.matchmaker') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('dashboard.matchmaker') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🏠</span> Dashboard
                </a>

                <p class="text-teal-500 text-xs uppercase tracking-widest px-3 pt-4 pb-1">Clients</p>

                <a href="{{ route('matchmaker.clients.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.clients.*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🗂️</span> My Clients
                </a>
                <a href="{{ route('matchmaker.clients.create') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.clients.create') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">➕</span> Add Client
                </a>

                <p class="text-teal-500 text-xs uppercase tracking-widest px-3 pt-4 pb-1">Nikah Profiles</p>

                <a href="{{ route('matchmaker.nikah.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.nikah.index') || request()->routeIs('matchmaker.nikah.show') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🔎</span> Browse Profiles
                </a>
                <a href="{{ route('matchmaker.nikah.requests') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('matchmaker.nikah.requests') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">📨</span> My Contact Requests
                </a>
            </nav>

            {{-- Bottom: Back to site --}}
            <div class="px-3 py-4 border-t border-teal-700 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-teal-300 hover:bg-teal-800 text-sm transition">
                    <span class="text-base">↩️</span> Back to Site
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-teal-300 hover:bg-teal-800 text-sm transition text-left">
                        <span class="text-base">🚪</span> Logout
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
                    <img src="{{ Auth::user()->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover">
                    <span class="text-sm text-gray-700 hidden sm:block">{{ Auth::user()->name }}</span>
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
