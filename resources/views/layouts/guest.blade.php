<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    {{-- Preload critical font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">

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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">
    <!-- Icon fonts (kept — swap for an SVG icon set later if you want to drop these) -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

    <!-- Vite: Tailwind + Alpine (ships with Breeze) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- ============================================================ --}}
    {{-- THEME TOKENS (mapped from the original :root vars) --}}
    {{-- Reference in Tailwind config as teal / gold / cream --}}
    {{-- or use arbitrary values like bg-[--teal] as done below. --}}
    {{-- ============================================================ --}}

</head>

<body class="antialiased font-sans">
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
                        <span class="text-gray-300 hidden lg:inline mr-1">Follow Us:</span>
                        <a class="text-gray-200 hover:text-white px-1" href="{{ setting('social_facebook') }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a class="text-gray-200 hover:text-white px-1" href="{{ setting('social_tiktok') }}" target="_blank"><i class="fab fa-tiktok"></i></a>
                        <a class="text-gray-200 hover:text-white px-1" href="{{ setting('social_youtube') }}" target="_blank"><i class="fab fa-youtube"></i></a>
                        <a class="text-gray-200 hover:text-white px-1 mr-2" href="{{ setting('social_instagram') }}" target="_blank"><i class="fab fa-instagram"></i></a>
                        @auth
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center rounded-md bg-[--teal] px-4 py-2 text-white text-sm font-medium hover:bg-[--teal-dark] transition">
                            <i class="fa fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                        @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center rounded-md border border-[--teal] px-4 py-2 text-[--teal] text-sm font-medium hover:bg-[--teal] hover:text-white transition">
                            <i class="fa fa-lock mr-1"></i> Log in
                        </a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center rounded-md border border-[--teal] px-4 py-2 text-[--teal] text-sm font-medium hover:bg-[--teal] hover:text-white transition ml-2">
                            <i class="fa fa-user-plus mr-1"></i> Register
                        </a>
                        @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="p-4 rounded-xl text-sm font-medium flex items-center gap-3"
                style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626">
                <i class="fa fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        </div>
        @endif
        <!-- Main nav -->
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex items-center justify-between py-2">
                <a href="{{ url('/') }}" class="shrink-0">
                    <x-application-logo />
                </a>
                <!-- Mobile: auth button before hamburger -->
                <div class="flex lg:hidden items-center gap-2 ml-auto mr-2">
                    @auth
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center rounded-md bg-[--teal] text-white text-sm font-medium px-3 py-1.5">
                        <i class="fa fa-th-large mr-1"></i>Dashboard
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center rounded-md border border-[--teal] text-[--teal] text-sm font-medium px-3 py-1.5">
                        Login
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
                        <a href="{{ url('/') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('/') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">Home</a>
                        <a href="{{ url('/about') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('about') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">About</a>
                        <a href="{{ url('/activities') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('activities') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">Activities</a>
                        <a href="{{ route('blog.index') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('blog.*') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">Blog</a>
                        <a href="{{ url('/team') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('team') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">Team</a>
                        <a href="{{ url('/contact') }}" class="px-3 py-2 text-sm font-medium {{ request()->is('contact') ? 'text-[--teal]' : 'text-[--text-dark] hover:text-[--teal]' }}">Contact</a>
                    </div>
                </div>
                <!-- Desktop CTA buttons -->
                <div class="hidden lg:flex items-center gap-2 ml-2">
                    <a href="{{ url('/donate') }}" class="inline-flex items-center rounded-md bg-[--teal] text-white font-medium py-2 px-4 hover:bg-[--teal-dark] transition">💝 Donate</a>
                    <a href="{{ url('/volunteer') }}" class="inline-flex items-center rounded-md border border-[--teal] text-[--teal] font-medium py-2 px-4 hover:bg-[--teal] hover:text-white transition">🤝 Volunteer</a>
                </div>
            </nav>
            <!-- Mobile collapse panel -->
            <div x-show="mobileOpen"
                x-cloak
                x-transition
                class="lg:hidden pb-4" id="mobile-menu">
                <div class="flex flex-col">
                    <a href="{{ url('/') }}" class="py-2 text-sm font-medium {{ request()->is('/') ? 'text-[--teal]' : 'text-[--text-dark]' }}">Home</a>
                    <a href="{{ url('/about') }}" class="py-2 text-sm font-medium {{ request()->is('about') ? 'text-[--teal]' : 'text-[--text-dark]' }}">About</a>
                    <a href="{{ url('/activities') }}" class="py-2 text-sm font-medium {{ request()->is('activities') ? 'text-[--teal]' : 'text-[--text-dark]' }}">Activities</a>
                    <a href="{{ route('blog.index') }}" class="py-2 text-sm font-medium {{ request()->routeIs('blog.*') ? 'text-[--teal]' : 'text-[--text-dark]' }}">Blog</a>
                    <a href="{{ url('/team') }}" class="py-2 text-sm font-medium {{ request()->is('team') ? 'text-[--teal]' : 'text-[--text-dark]' }}">Team</a>
                    <a href="{{ url('/contact') }}" class="py-2 text-sm font-medium {{ request()->is('contact') ? 'text-[--teal]' : 'text-[--text-dark]' }}">Contact</a>
                    <div class="border-t my-2"></div>
                    <a href="{{ url('/donate') }}" class="py-2 text-sm font-semibold text-[--teal]">💝 Donate</a>
                    <a href="{{ url('/volunteer') }}" class="py-2 text-sm font-semibold text-[--teal]">🤝 Volunteer</a>
                    <a href="{{ route('courses.index') }}" class="py-2 text-sm">📖 Quran Courses</a>
                    <a href="{{ route('quran-live.index') }}" class="py-2 text-sm">🎥 Live Classes</a>
                    @guest
                    <a href="{{ route('register') }}" class="py-2 text-sm font-semibold">📝 Register Free</a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
    <!-- /Header -->
    <main class="w-full px-2 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </main>
    <!-- Footer -->
    <div class="w-full bg-[--text-dark] pt-12">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pb-8">
                <div class="lg:col-span-7">
                    <h2 class="text-white text-2xl font-semibold mb-0">Subscribe our newsletter</h2>
                    <p class="text-gray-400">Get the latest news and other tips</p>
                </div>
                <div class="lg:col-span-5">
                    <div class="relative mx-auto">
                        <form id="subscribeForm" action="{{ route('subscribe') }}" method="POST">
                            @csrf
                            <div class="relative mx-auto">
                                <input name="email"
                                    type="email"
                                    required
                                    placeholder="Enter your email"
                                    class="w-full rounded-md border-0 py-3 pl-4 pr-28 focus:ring-2 focus:ring-[--teal] focus:outline-none">
                                <button type="submit"
                                    class="absolute top-1/2 -translate-y-1/2 right-2 inline-flex items-center rounded-md bg-[--teal] text-white py-2 px-4 hover:bg-[--teal-dark] transition">
                                    Subscribe
                                </button>
                            </div>
                        </form>
                        @if(session('success'))
                        <small class="text-green-400 block mt-2">{{ session('success') }}</small>
                        @endif
                        @if(session('info'))
                        <small class="text-yellow-400 block mt-2">{{ session('info') }}</small>
                        @endif
                        @if($errors->has('email'))
                        <small class="text-red-400 block mt-2">{{ $errors->first('email') }}</small>
                        @endif
                        <div id="subscribe-message" class="mt-2"></div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8 pt-8">
                <div>
                    <img src="{{ asset('img/logo-w.png')}}" class="max-w-full h-auto">
                    <p class="my-4 text-gray-400">Sallaamti (سلامتی) is an organization dedicated to spreading peace, knowledge, and compassion through the teachings of the Quran and Hadith.</p>
                    <a href="{{ url('/donate') }}" class="inline-flex items-center rounded-md bg-[--teal] text-white font-medium py-2 px-4 hover:bg-[--teal-dark] transition">Donate Now</a>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact</h4>
                    <div class="flex flex-col">
                        <h6 class="text-gray-400 mb-0 text-sm">Our Address</h6>
                        <div class="flex items-center border-b border-gray-700 py-4">
                            <span class="shrink-0 flex items-center justify-center w-12 h-12 bg-[--teal] mr-3">
                                <i class="fa fa-map-marker-alt text-white"></i>
                            </span>
                            <a href="{{ url('/contact') }}" class="text-gray-300 hover:text-white">{{ setting('site_address') }}</a>
                        </div>
                        <h6 class="text-gray-400 mt-4 mb-0 text-sm">Our Mobile</h6>
                        <div class="flex items-center py-4">
                            <span class="shrink-0 flex items-center justify-center w-12 h-12 bg-[--teal] mr-3">
                                <i class="fa fa-phone-alt text-white"></i>
                            </span>
                            <a href="https://wa.me/{{ setting('social_whatsapp') }}" class="text-gray-300 hover:text-white" target="_blank">{{ setting('site_phone') }}</a>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Explore Link</h4>
                    <div class="flex flex-col items-start">
                        <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/home') }}"><i class="fa fa-check text-[--teal] mr-2"></i>Home</a>
                        <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/about') }}"><i class="fa fa-check text-[--teal] mr-2"></i>About Us</a>
                        <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/activities') }}"><i class="fa fa-check text-[--teal] mr-2"></i>Activities</a>
                        <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/contact') }}"><i class="fa fa-check text-[--teal] mr-2"></i>Contact us</a>
                        <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/donate') }}"><i class="fa fa-check text-[--teal] mr-2"></i>Donations</a>
                        <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/volunteer') }}"><i class="fa fa-check text-[--teal] mr-2"></i>Become Volunteer</a>
                        <a class="text-gray-300 hover:text-white mb-2" href="{{ route('certificate.verify') }}"><i class="fa fa-check text-[--teal] mr-2"></i>Verify Certificate / ID</a>
                    </div>
                </div>
                <div>
                    <h5 class="text-white mb-4 font-semibold">Latest Posts</h5>

                    @if (isset($footerPosts) && $footerPosts->isNotEmpty())
                    <div class="flex flex-col gap-0">
                        @foreach ($footerPosts as $post)
                        <a href="{{ url('/blog/' . $post->slug) }}"
                            class="flex items-start gap-3 py-4 border-b border-gray-700 last:border-0 group">

                            {{-- Thumbnail --}}
                            <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gray-700">
                                @if ($post->thumbnail)
                                <img src="{{ Storage::url($post->thumbnail) }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    alt="{{ $post->title }}">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-2xl">📝</div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-500 text-xs mb-1 flex items-center gap-1">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $post->published_at?->format('d M Y') }}
                                    @if ($post->category)
                                    <span class="text-gray-600">·</span>
                                    <span>{{ $post->category }}</span>
                                    @endif
                                </p>
                                <p class="text-gray-300 text-sm leading-snug group-hover:text-white transition-colors line-clamp-2">
                                    {{ $post->title }}
                                </p>
                            </div>

                        </a>
                        @endforeach
                    </div>

                    <a href="{{ url('/blog') }}"
                        class="inline-flex items-center gap-1 text-xs mt-3 transition-colors"
                        style="color: #b8962e">
                        View all posts
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    @else
                    {{-- No posts yet — show placeholder --}}
                    <div class="flex flex-col gap-0">
                        @foreach ([
                        ['📖', 'How to Learn Quran Effectively Online', '15 Jan 2025'],
                        ['💍', 'Islamic Guide to Finding a Halal Spouse', '10 Jan 2025'],
                        ] as $placeholder)
                        <div class="flex items-start gap-3 py-4 border-b border-gray-700 last:border-0 opacity-40">
                            <div class="flex-shrink-0 w-16 h-16 rounded-lg bg-gray-700 flex items-center justify-center text-2xl">
                                {{ $placeholder[0] }}
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-500 text-xs mb-1">{{ $placeholder[2] }}</p>
                                <p class="text-gray-400 text-sm leading-snug">{{ $placeholder[1] }}</p>
                            </div>
                        </div>
                        @endforeach
                        <p class="text-xs text-gray-600 mt-3">Blog articles coming soon...</p>
                    </div>
                    @endif

                </div>
            </div>
        </div>
        <div class="border-t border-gray-700">
            <div class="max-w-7xl mx-auto px-4 py-4">
                <div class="flex flex-col md:flex-row items-center justify-between gap-2 text-gray-400 text-sm">
                    <div class="text-center md:text-left">
                        &copy; <a class="border-b border-gray-500" href="#">www.sallaamti.com</a>, All Right Reserved.
                    </div>
                    <div class="text-center md:text-right">
                        Designed & Developed By <a class="border-b border-gray-500" href="https://izmadts.com">IZMAdts</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Footer -->
    <!-- Back to Top -->
    <a href="#"
        x-data="{ show: false }"
        x-init="window.addEventListener('scroll', () => show = window.scrollY > 300)"
        x-show="show"
        x-cloak
        x-transition
        class="fixed bottom-6 right-6 z-50 inline-flex items-center justify-center w-11 h-11 rounded-md bg-[--teal] text-white border-2 border-white shadow-lg hover:bg-[--teal-dark] transition">
        <i class="fa fa-arrow-up"></i>
    </a>
    <!-- Newsletter subscribe AJAX (jQuery removed — vanilla fetch) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('subscribeForm');
            const msg = document.getElementById('subscribe-message');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            msg.innerHTML = '<span class="text-green-400">' + data.message + '</span>';
                            form.reset();
                            window.dataLayer = window.dataLayer || [];
                            dataLayer.push({
                                event: 'newsletter_signup'
                            });
                        } else {
                            msg.innerHTML = '<span class="text-yellow-400">' + data.message + '</span>';
                        }
                    })
                    .catch(() => {
                        msg.innerHTML = '<span class="text-red-400">Invalid email address.</span>';
                    });
            });
        });
    </script>
</body>

</html>