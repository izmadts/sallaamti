<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}">

<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
 <meta name="theme-color" content="#0d6b6b">
 <meta name="mobile-web-app-capable" content="yes">
 <meta name="apple-mobile-web-app-capable" content="yes">
 <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
 <link rel="manifest" href="{{ asset('site.webmanifest') }}">
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <meta name="vapid-public-key" content="{{ config('services.webpush.public_key') }}">
 {{-- ===== PRIMARY META ===== --}}
 <title>@yield('title', setting('seo_home_title', setting('site_name') . ' | ' . setting('site_tagline')))</title>
 <meta name="description" content="@yield('description', setting('seo_home_description', 'Learn Quran online with Sallaamti.'))">
 <meta name="keywords" content="@yield('keywords', setting('seo_keywords', 'learn quran online'))">

 {{-- Google verification --}}
 @if(setting('google_verification'))
 <meta name="google-site-verification" content="{{ setting('google_verification') }}">
 @endif
 <meta name="author" content="{{ setting('site_name', 'Sallaamti') }}">
 <meta name="robots" content="@yield('robots', 'index, follow')">
 <link rel="canonical" href="@yield('canonical', url()->current())">

 {{-- ===== OPEN GRAPH (Facebook, WhatsApp previews) ===== --}}
 <meta property="og:type" content="@yield('og_type', 'website')">
 <meta property="og:title" content="@yield('og_title', setting('site_name') . ' | ' . setting('site_tagline'))">
 <meta property="og:description" content="@yield('og_description', 'Learn Quran online with expert teachers. Self-paced courses, live classes, Islamic matrimonial and family support.')">
 <meta property="og:image" content="@yield('og_image', asset('img/og-default.jpg'))">
 <meta property="og:url" content="{{ url()->current() }}">
 <meta property="og:site_name" content="{{ setting('site_name', 'Sallaamti') }}">
 <meta property="og:locale" content="en_PK">

 {{-- ===== TWITTER CARD ===== --}}
 <meta name="twitter:card" content="summary_large_image">
 <meta name="twitter:title" content="@yield('og_title', setting('site_name'))">
 <meta name="twitter:description" content="@yield('og_description', 'Learn Quran online with expert teachers.')">
 <meta name="twitter:image" content="@yield('og_image', asset('img/og-default.jpg'))">
 {{-- ===== STRUCTURED DATA (JSON-LD) ===== --}}
 @php
 $structuredData = [
 '@context' => 'https://schema.org',
 '@type' => 'Organization',
 'name' => setting('site_name', 'Sallaamti'),
 'url' => config('app.url'),
 'logo' => asset('img/logo.png'),
 'description' => setting('about_text', 'Islamic education platform offering Quran courses, live classes and matrimonial services.'),
 'contactPoint' => [
 '@type' => 'ContactPoint',
 'telephone' => setting('site_phone', ''),
 'email' => setting('site_email', ''),
 'contactType' => 'customer support',
 ],
 'sameAs' => array_filter([
 setting('social_facebook', ''),
 setting('social_instagram', ''),
 setting('social_youtube', ''),
 setting('social_tiktok', ''),
 ]),
 ];
 @endphp
 <script type="application/ld+json">
 {
 !!json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!
 }
 </script>
 {{-- Page-specific structured data --}}
 @stack('schema')

 {{-- Favicon --}}
 <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
 <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

 {{-- Preload hero image (first banner) --}}
 @php $firstBanner = \App\Models\Banner::active()->first(); @endphp
 @if ($firstBanner)
 <link rel="preload" as="image"
 href="{{ str_starts_with($firstBanner->image, 'img/') ? asset($firstBanner->image) : Storage::url($firstBanner->image) }}">
 @endif
 {{-- ... rest of your CSS links unchanged ... --}}

 @include('partials.gtm-head')

 <!-- Fonts -->
 <link rel="preconnect" href="https://fonts.bunny.net">
 <link rel="preconnect" href="https://fonts.googleapis.com">
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 <link rel="preconnect" href="https://use.fontawesome.com" crossorigin>
 <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
 <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 <!-- Icon font -->
 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />

 <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

 <!-- Vite: Tailwind + Alpine (ships with Breeze) -->
 @vite(['resources/css/app.css', 'resources/js/app.js'])
 {{-- ============================================================ --}}
 {{-- THEME TOKENS (mapped from the original :root vars) --}}
 {{-- Reference in Tailwind config as teal / gold / cream --}}
 {{-- or use arbitrary values like bg-[--teal] as done below. --}}
 {{-- ============================================================ --}}

</head>

<body class="antialiased font-sans pb-16 sm:pb-0">
 @include('partials.gtm-body')
 <!-- Spinner -->
 <div id="spinner"
 class="fixed inset-0 z-50 flex items-center justify-center bg-white w-full h-full"
 x-data
 x-init="setTimeout(() => $el.remove(), 800)">
 <div class="w-10 h-10 border-4 border-[--teal] border-t-transparent rounded-full animate-spin"></div>
 </div>
 <!-- Header / Topbar -->
 <div id="header" class="w-full border-b sticky top-0 z-40 bg-white" x-data="{ mobileOpen: false }">
 <!-- Top info bar (desktop only) -->
 <div class="hidden md:block bg-[--teal-dark]">
 <div class="max-w-7xl mx-auto px-4">
 <div class="flex items-center justify-between py-2 text-sm">
 <div class="flex items-center gap-6">
 <a href="https://wa.me/{{ setting('social_whatsapp') }}" class="flex items-center text-gray-200 hover:text-white">
 <span class="fa fa-phone-alt mr-2"></span>
 <span>{{ setting('site_phone') }}</span>
 </a>
 <a href="mailto:{{ setting('site_email') }}" class="flex items-center text-gray-200 hover:text-white">
 <span class="far fa-envelope mr-2"></span>
 <span>{{ setting('site_email') }}</span>
 </a>
 </div>
 <div class="flex items-center gap-3">
 <span class="text-gray-300 hidden lg:inline mr-1">{{ __('db.Follow Us:') }}</span>
 <a class="text-gray-200 hover:text-white px-1" href="{{ setting('social_facebook') }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
 <a class="text-gray-200 hover:text-white px-1" href="{{ setting('social_tiktok') }}" target="_blank"><i class="fab fa-tiktok"></i></a>
 <a class="text-gray-200 hover:text-white px-1" href="{{ setting('social_youtube') }}" target="_blank"><i class="fab fa-youtube"></i></a>
 <a class="text-gray-200 hover:text-white px-1 mr-2" href="{{ setting('social_instagram') }}" target="_blank"><i class="fab fa-instagram"></i></a>
 <x-language-switcher dark />
 @auth
 <a href="{{ route('dashboard') }}"
 class="inline-flex items-center rounded-md bg-[--teal] px-4 py-2 text-white text-sm font-medium hover:bg-[--teal-dark] transition">
 <i class="fa fa-tachometer-alt mr-1"></i> {{ __('db.Dashboard') }}
 </a>
 <form method="POST" action="{{ route('logout') }}" class="inline-flex">
 @csrf
 <button type="submit"
 class="inline-flex items-center rounded-md border border-gray-500 px-4 py-2 text-gray-200 text-sm font-medium hover:bg-white hover:text-[--teal-dark] transition">
 <i class="fa fa-sign-out-alt mr-1"></i> {{ __('db.Log Out') }}
 </button>
 </form>
 @else
 <a href="{{ route('login') }}"
 class="inline-flex items-center rounded-md border border-[--teal] px-4 py-2 text-[--teal] text-sm font-medium hover:bg-[--teal] hover:text-white transition">
 <i class="fa fa-lock mr-1"></i> {{ __('db.Log in') }}
 </a>
 @if (Route::has('register'))
 <a href="{{ route('register') }}"
 class="inline-flex items-center rounded-md border border-[--teal] px-4 py-2 text-[--teal] text-sm font-medium hover:bg-[--teal] hover:text-white transition ml-2">
 <i class="fa fa-user-plus mr-1"></i> {{ __('db.Register') }}
 </a>
 @endif
 @endauth
 </div>
 </div>
 </div>
 </div>
 <!-- Main nav -->
 <div class="max-w-7xl mx-auto px-4">
 <nav class="flex items-center justify-between py-2">
 <a href="{{ url('/') }}" class="shrink-0">
 <x-application-logo />
 </a>
 <!-- Mobile: language switcher + auth button before hamburger -->
 <div class="flex lg:hidden items-center gap-2 ml-auto mr-2">
 <x-language-switcher compact />
 @auth
 <a href="{{ route('dashboard') }}"
 class="inline-flex items-center rounded-md bg-[--teal] text-white text-sm font-medium px-3 py-1.5">
 <i class="fa fa-th-large mr-1"></i>{{ __('db.Dashboard') }}
 </a>
 <form method="POST" action="{{ route('logout') }}" class="inline-flex">
 @csrf
 <button type="submit"
 class="inline-flex items-center rounded-md border border-[--teal] text-[--teal] text-sm font-medium px-3 py-1.5">
 <i class="fa fa-sign-out-alt"></i>
 </button>
 </form>
 @else
 <a href="{{ route('login') }}"
 class="inline-flex items-center rounded-md border border-[--teal] text-[--teal] text-sm font-medium px-3 py-1.5">
 {{ __('db.Login') }}
 </a>
 @endauth
 </div>
 <!-- Hamburger -->
 <button type="button"
 @click="mobileOpen = !mobileOpen"
 class="lg:hidden border-0 shadow-none p-2 focus:outline-none"
 aria-label="Toggle navigation menu"
 :aria-expanded="mobileOpen.toString()"
 aria-controls="mobile-menu">
 <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
 <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen}"
 class="inline-flex" stroke-linecap="round" stroke-linejoin="round"
 stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
 <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen}"
 class="hidden" stroke-linecap="round" stroke-linejoin="round"
 stroke-width="2" d="M6 18L18 6M6 6l12 12" />
 </svg>
 <span class="sr-only">Toggle menu</span>
 </button>
 <!-- Nav links -->
 <div class="hidden lg:flex lg:items-center lg:ml-auto lg:mx-auto">
 <div class="flex items-center gap-1">
 <a href="{{ url('/') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('/') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.Home') }}</a>
 <a href="{{ url('/about') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('about') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.About') }}</a>
 <a href="{{ url('/activities') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('activities') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.Activities') }}</a>
 <a href="{{ route('blog.index') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('blog.*') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.Blog') }}</a>
 <a href="{{ url('/events') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('events') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.Events') }}</a>
 <a href="{{ url('/team') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('team') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.Team') }}</a>
 <a href="{{ url('/contact') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('contact') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.Contact') }}</a>
 </div>
 </div>
 <!-- Desktop CTA buttons -->
 <div class="hidden lg:flex items-center gap-2 ml-2">
 <x-wall-nav-badge :dua="$latestApprovedDua ?? null">
 <a href="{{ route('wall.index') }}" class="inline-flex items-center gap-1 rounded-full text-white font-semibold text-sm py-2 px-4 hover:brightness-110 transition" style="background: var(--teal)">
 🤲 {{ __('db.Sallaamti Wall') }}
 <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: var(--gold)"></span>
 </a>
 </x-wall-nav-badge>
 <a href="{{ route('nikah.create') }}" class="inline-flex items-center rounded-full text-white font-semibold text-sm py-2 px-4 hover:brightness-110 transition" style="background: #b8962e">💍 {{ __('db.Find a Match') }}</a>
 <a href="{{ route('counseling.book.start') }}" class="inline-flex items-center rounded-full border border-[--teal] text-[--teal] font-semibold text-sm py-2 px-4 hover:bg-[--teal] hover:text-white transition">🤝 {{ __('db.Get Counseling') }}</a>
 <a href="{{ url('/donate') }}" class="inline-flex items-center rounded-md bg-[--teal] text-white font-medium py-2 px-4 hover:bg-[--teal-dark] transition">💝 {{ __('db.Donate') }}</a>
 <a href="{{ url('/volunteer') }}" class="inline-flex items-center rounded-md border border-[--teal] text-[--teal] font-medium py-2 px-4 hover:bg-[--teal] hover:text-white transition">🤝 {{ __('db.Volunteer') }}</a>
 </div>
 </nav>
 <!-- Mobile collapse panel -->
 <div x-show="mobileOpen"
 x-cloak
 x-transition
 class="lg:hidden pb-4" id="mobile-menu">
 <div class="flex flex-col">
 <a href="{{ url('/') }}" class="py-2 text-sm font-medium {{ request()->is('/') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.Home') }}</a>
 <a href="{{ url('/about') }}" class="py-2 text-sm font-medium {{ request()->is('about') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.About') }}</a>
 <a href="{{ url('/activities') }}" class="py-2 text-sm font-medium {{ request()->is('activities') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.Activities') }}</a>
 <a href="{{ route('blog.index') }}" class="py-2 text-sm font-medium {{ request()->routeIs('blog.*') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.Blog') }}</a>
 <a href="{{ url('/events') }}" class="py-2 text-sm font-medium {{ request()->is('events') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.Events') }}</a>
 <a href="{{ url('/team') }}" class="py-2 text-sm font-medium {{ request()->is('team') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.Team') }}</a>
 <a href="{{ url('/contact') }}" class="py-2 text-sm font-medium {{ request()->is('contact') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.Contact') }}</a>
 <div class="border-t my-2"></div>
 <a href="{{ route('wall.index') }}" class="py-2 text-sm font-semibold flex items-center gap-1.5" style="color: var(--teal)">
 🤲 {{ __('db.Sallaamti Wall') }}
 @if ($latestApprovedDua ?? null)
 <span class="w-1.5 h-1.5 rounded-full animate-pulse inline-block" style="background: var(--gold)"></span>
 @endif
 </a>
 <a href="{{ route('nikah.create') }}" class="py-2 text-sm font-semibold" style="color: #b8962e">💍 {{ __('db.Find a Match') }}</a>
 <a href="{{ route('counseling.book.start') }}" class="py-2 text-sm font-semibold text-[--teal]">🤝 {{ __('db.Get Counseling') }}</a>
 <a href="{{ url('/donate') }}" class="py-2 text-sm font-semibold text-[--teal]">💝 {{ __('db.Donate') }}</a>
 <a href="{{ url('/volunteer') }}" class="py-2 text-sm font-semibold text-[--teal]">🤝 {{ __('db.Volunteer') }}</a>
 <a href="{{ route('courses.index') }}" class="py-2 text-sm">📖 {{ __('db.Quran Courses') }}</a>
 <a href="{{ route('quran-live.index') }}" class="py-2 text-sm">🎥 {{ __('db.Live Classes') }}</a>
 @guest
 <a href="{{ route('register') }}" class="py-2 text-sm font-semibold">📝 {{ __('db.Register Free') }}</a>
 @endguest
 </div>
 </div>
 </div>
 </div>
 <!-- /Header -->
 <main class="w-full px-2 bg-white shadow-md overflow-hidden sm:rounded-lg">
 @if (isset($errors) && $errors->any())
 <div class="max-w-4xl mx-auto px-4 pt-6">
 <x-alert type="error">
 <strong>{{ __('db.Please fix the following:') }}</strong>
 <ul class="list-disc ms-5 mt-1">
 @foreach ($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </x-alert>
 </div>
 @endif
 @if (session('success') || session('status') || session('error') || session('info') || session('warning'))
 <div class="max-w-4xl mx-auto px-4 pt-6">
 <x-flash-messages />
 </div>
 @endif
 {{ $slot }}
 </main>
 @include('partials.footer')
 @include('components.pwa-install-banner')

 {{-- ===== MOBILE APP-STYLE BOTTOM TAB BAR (guests) ===== --}}
 <div class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 safe-bottom-nav shadow-[0_-2px_8px_rgba(0,0,0,0.06)]">
 <div class="grid grid-cols-5">
 <a href="{{ url('/') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-medium {{ request()->is('/') ? 'text-[--teal]' : 'text-gray-500' }}">
 <span class="text-lg leading-none">🏠</span>
 {{ __('db.Home') }}
 </a>
 <a href="{{ route('courses.index') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-medium {{ request()->routeIs('courses.*') || request()->routeIs('quran-live.*') ? 'text-[--teal]' : 'text-gray-500' }}">
 <span class="text-lg leading-none">📖</span>
 {{ __('db.Quran') }}
 </a>
 <a href="{{ route('nikah.create') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-medium {{ request()->routeIs('nikah.*') ? 'text-[--teal]' : 'text-gray-500' }}">
 <span class="text-lg leading-none">💍</span>
 {{ __('db.Nikah') }}
 </a>
 <a href="{{ route('counseling.book.start') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-medium {{ request()->routeIs('counseling.*') ? 'text-[--teal]' : 'text-gray-500' }}">
 <span class="text-lg leading-none">🤝</span>
 {{ __('db.Support') }}
 </a>
 <a href="{{ route('register') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-semibold" style="color: var(--gold)">
 <span class="text-lg leading-none">✨</span>
 {{ __('db.Join') }}
 </a>
 </div>
 </div>
</body>

</html>
