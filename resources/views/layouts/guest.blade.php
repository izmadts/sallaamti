<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}" translate="no">

<head>
 <meta charset="utf-8">
 <meta name="google" content="notranslate">
 <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
 <meta name="theme-color" content="#0d6b6b">
 <meta name="mobile-web-app-capable" content="yes">
 <meta name="apple-mobile-web-app-capable" content="yes">
 <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
 <link rel="manifest" href="{{ asset('site.webmanifest') }}">
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <meta name="vapid-public-key" content="{{ config('services.webpush.public_key') }}">
 {{-- ===== PRIMARY META =====
      Two ways a page can override these, both supported: call
      @section('title', ...) explicitly (what index.blade.php does), or
      pass :title/:description/:image props to <x-guest-layout> (what
      blog/show.blade.php and others do) — those props land here as
      $title/$description/$image via GuestLayout's component class.
      Previously only @section worked; the prop path was silently a
      no-op, so every page using it (e.g. individual blog posts) showed
      the generic site-wide title/description instead of its own. --}}
 @php
     $pageTitle = $title ?? setting('seo_home_title', setting('site_name') . ' | ' . setting('site_tagline'));
     $pageDescription = $description ?? setting('seo_home_description', 'Learn Quran online with Sallaamti.');
     $defaultOgImage = $image ?? (setting('seo_og_image') ? \Illuminate\Support\Facades\Storage::disk('public')->url(setting('seo_og_image')) : asset('img/og-default.jpg'));
 @endphp
 <title>@yield('title', $pageTitle)</title>
 <meta name="description" content="@yield('description', $pageDescription)">
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
 <meta property="og:title" content="@yield('og_title', $pageTitle)">
 <meta property="og:description" content="@yield('og_description', $pageDescription)">
 <meta property="og:image" content="@yield('og_image', $defaultOgImage)">
 <meta property="og:url" content="{{ url()->current() }}">
 <meta property="og:site_name" content="{{ setting('site_name', 'Sallaamti') }}">
 <meta property="og:locale" content="en_PK">

 {{-- ===== TWITTER CARD ===== --}}
 <meta name="twitter:card" content="summary_large_image">
 <meta name="twitter:title" content="@yield('og_title', $pageTitle)">
 <meta name="twitter:description" content="@yield('og_description', $pageDescription)">
 <meta name="twitter:image" content="@yield('og_image', $defaultOgImage)">
 {{-- ===== STRUCTURED DATA (JSON-LD) ===== --}}
 @php
 $structuredData = [
 '@context' => 'https://schema.org',
 '@type' => 'Organization',
 'name' => setting('site_name', 'Sallaamti'),
 'url' => config('app.url'),
 'logo' => asset('img/logo.png'),
 'description' => strip_tags(setting('about_text', 'Islamic education platform offering Quran courses, live classes and matrimonial services.')),
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
 <!-- Spinner — Alpine removes it after 800ms in the normal case. If
      Alpine never initializes (blocked/slow script on a mobile
      connection, a JS error earlier in the page), this pure-CSS
      animation is a guaranteed fallback that doesn't depend on any
      JavaScript running at all, so the page can never get stuck
      permanently covered in white. -->
 <style>
 @keyframes spinner-failsafe-hide {
 to { opacity: 0; visibility: hidden; }
 }
 #spinner {
 animation: spinner-failsafe-hide 0.3s ease-out 2.5s forwards;
 }
 </style>
 <div id="spinner"
 class="fixed inset-0 z-50 flex items-center justify-center bg-white w-full h-full"
 x-data
 x-init="setTimeout(() => $el.remove(), 800)">
 <div class="w-10 h-10 border-4 border-[--teal] border-t-transparent rounded-full animate-spin"></div>
 </div>
 <!-- Header / Topbar — a single clean row: logo + nav links on the left,
      language + auth on the right. Phone/email/social moved to the footer
      so this bar stays minimal and the focus stays on Log In / Register. -->
 <div id="header" class="w-full border-b sticky top-0 z-40 bg-white" x-data="{ mobileOpen: false }">
 <div class="max-w-7xl mx-auto px-4">
 <nav class="flex items-center justify-between py-3 gap-3">

 {{-- Logo --}}
 <a href="{{ url('/') }}" class="shrink-0 flex items-center">
 <x-application-logo class="h-9 w-auto" />
 </a>

 {{-- Desktop nav links — left side, right after the logo --}}
 <div class="hidden lg:flex items-center gap-1">
 <a href="{{ url('/') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('/') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.Home') }}</a>
 <a href="{{ url('/about') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('about') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.About') }}</a>
 <a href="{{ route('blog.index') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('blog.*') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.Blog') }}</a>
 <a href="{{ url('/team') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('team') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.Team') }}</a>
 <a href="{{ url('/contact') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('contact') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">{{ __('db.Contact') }}</a>
 @if (!auth()->check() || !auth()->user()->hasAnyRole(['admin', 'teacher', 'family_counselor', 'matchmaker', 'manager', 'blogger']))
 <x-wall-nav-badge :dua="$latestApprovedDua ?? null">
 <a href="{{ route('wall.index') }}" class="px-3 py-2 text-sm font-medium inline-flex items-center gap-1 {{ request()->routeIs('wall.*') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">
 🤲 {{ __('db.Wall') }}
 <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: var(--gold)"></span>
 </a>
 </x-wall-nav-badge>
 @endif
 </div>

 {{-- Spacer pushes the right-side block to the edge --}}
 <div class="flex-1 hidden lg:block"></div>

 {{-- Desktop right: language + auth --}}
 <div class="hidden lg:flex items-center gap-3 shrink-0">
 <x-language-switcher />
 @auth
 <a href="{{ route('dashboard') }}"
 class="inline-flex items-center rounded-lg bg-[--teal] px-4 py-2 text-white text-sm font-semibold hover:bg-[--teal-dark] transition">
 {{ __('db.Dashboard') }}
 </a>
 @else
 <a href="{{ route('login') }}"
 class="text-[--text-dark] text-sm font-medium px-3 py-2 hover:text-[--teal] transition">
 {{ __('db.Log In') }}
 </a>
 @if (Route::has('register'))
 <a href="{{ route('register') }}"
 class="inline-flex items-center rounded-lg text-white text-sm font-semibold px-4 py-2 hover:brightness-110 transition"
 style="background: #b8962e">
 {{ __('db.Register Free') }}
 </a>
 @endif
 @endauth
 </div>

 {{-- Mobile: compact language + register + hamburger --}}
 <div class="flex lg:hidden items-center gap-2 ml-auto">
 <x-language-switcher compact />
 @guest
 <a href="{{ route('register') }}"
 class="inline-flex items-center rounded-lg text-white text-xs font-semibold px-3 py-1.5"
 style="background: #b8962e">
 {{ __('db.Register') }}
 </a>
 @else
 <a href="{{ route('dashboard') }}"
 class="inline-flex items-center rounded-lg bg-[--teal] text-white text-xs font-semibold px-3 py-1.5">
 {{ __('db.Dashboard') }}
 </a>
 @endguest
 <button type="button"
 @click="mobileOpen = !mobileOpen"
 class="border-0 shadow-none p-2 focus:outline-none"
 aria-label="{{ __('db.Toggle navigation menu') }}"
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
 <span class="sr-only">{{ __('db.Toggle menu') }}</span>
 </button>
 </div>
 </nav>

 {{-- Mobile collapse panel --}}
 <div x-show="mobileOpen"
 x-cloak
 x-transition
 class="lg:hidden pb-4" id="mobile-menu">
 <div class="flex flex-col">
 <a href="{{ url('/') }}" class="py-2 text-sm font-medium {{ request()->is('/') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.Home') }}</a>
 <a href="{{ url('/about') }}" class="py-2 text-sm font-medium {{ request()->is('about') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.About') }}</a>
 <a href="{{ route('blog.index') }}" class="py-2 text-sm font-medium {{ request()->routeIs('blog.*') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.Blog') }}</a>
 <a href="{{ url('/team') }}" class="py-2 text-sm font-medium {{ request()->is('team') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.Team') }}</a>
 <a href="{{ url('/contact') }}" class="py-2 text-sm font-medium {{ request()->is('contact') ? 'text-[--teal]' : 'text-[--text-dark]' }}">{{ __('db.Contact') }}</a>
 @if (!auth()->check() || !auth()->user()->hasAnyRole(['admin', 'teacher', 'family_counselor', 'matchmaker', 'manager', 'blogger']))
 <div class="border-t my-2"></div>
 <a href="{{ route('wall.index') }}" class="py-2 text-sm font-semibold flex items-center gap-1.5" style="color: var(--teal)">
 🤲 {{ __('db.Sallaamti Wall') }}
 @if ($latestApprovedDua ?? null)
 <span class="w-1.5 h-1.5 rounded-full animate-pulse inline-block" style="background: var(--gold)"></span>
 @endif
 </a>
 @endif
 <a href="{{ route('nikah.create') }}" class="py-2 text-sm font-semibold" style="color: #b8962e">💍 {{ __('db.Find a Match') }}</a>
 <a href="{{ route('counseling.book.start') }}" class="py-2 text-sm font-semibold text-[--teal]">🤝 {{ __('db.Get Counseling') }}</a>
 <a href="{{ route('courses.index') }}" class="py-2 text-sm">📖 {{ __('db.Quran Courses') }}</a>
 <a href="{{ route('quran-live.index') }}" class="py-2 text-sm">🎥 {{ __('db.Live Classes') }}</a>
 <a href="{{ route('skills.index') }}" class="py-2 text-sm">💻 {{ __('db.Digital Skills') }}</a>
 <a href="{{ url('/donate') }}" class="py-2 text-sm font-semibold text-[--teal]">💝 {{ __('db.Donate') }}</a>
 <a href="{{ url('/volunteer') }}" class="py-2 text-sm font-semibold text-[--teal]">🤝 {{ __('db.Volunteer') }}</a>
 <div class="border-t my-2"></div>
 @guest
 <a href="{{ route('login') }}" class="py-2 text-sm font-semibold text-[--teal]">{{ __('db.Log In') }}</a>
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
 <div class="grid grid-cols-6">
 <a href="{{ url('/') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-medium {{ request()->is('/') ? 'text-[--teal]' : 'text-gray-500' }}">
 <span class="text-lg leading-none">🏠</span>
 {{ __('db.Home') }}
 </a>
 <a href="{{ route('courses.index') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-medium {{ request()->routeIs('courses.*') || request()->routeIs('quran-live.*') ? 'text-[--teal]' : 'text-gray-500' }}">
 <span class="text-lg leading-none">📖</span>
 {{ __('db.Quran') }}
 </a>
 <a href="{{ route('skills.index') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-medium {{ request()->routeIs('skills.*') ? 'text-[--teal]' : 'text-gray-500' }}">
 <span class="text-lg leading-none">💻</span>
 {{ __('db.Skills') }}
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
