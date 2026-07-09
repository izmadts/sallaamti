<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' — ' . setting('site_name') : setting('site_name') . ' | ' . setting('site_tagline') }}</title>
    <meta name="description" content="{{ $description ?? setting('site_tagline') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Twitter Card (used by WhatsApp/Facebook link previews) --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? setting('site_name') }}">
    <meta property="og:description" content="{{ $description ?? setting('site_tagline') }}">
    <meta property="og:image" content="{{ $image ?? asset('images/sallaamti-logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? setting('site_name') }}">
    <meta name="twitter:description" content="{{ $description ?? setting('site_tagline') }}">
    <meta name="twitter:image" content="{{ $image ?? asset('images/sallaamti-logo.png') }}">

    @include('partials.gtm-head')
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
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
    <style>
        /* :root {
            --teal: {{setting('theme_teal')}};
            --teal-light: {{setting('theme_teal_light')}};
            --teal-dark: {{setting('theme_teal_dark')}};
            --gold: {{setting('theme_gold')}};
            --cream: {{setting('theme_cream')}};
            --text-dark: {{setting('theme_text_dark')}};
        } */
        /* ===== AUTH FORMS ===== */
        .auth-wrapper {
            min-height: calc(100vh - 140px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 16px;
            background: var(--cream);
        }
        .auth-card {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
            padding: 48px 40px;
        }
        .auth-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .auth-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .auth-icon {
            position: absolute;
            left: 14px;
            color: #9ca3af;
            font-size: 13px;
            z-index: 1;
            pointer-events: none;
        }
        .auth-input {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #1f2937;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
        }
        .auth-input:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(13, 107, 107, 0.1);
        }
        .auth-input-error {
            border-color: #f87171 !important;
        }
        .auth-eye-btn {
            position: absolute;
            right: 12px;
            color: #9ca3af;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            font-size: 13px;
        }
        .auth-eye-btn:hover {
            color: var(--teal);
        }
        .auth-checkbox {
            width: 16px;
            height: 16px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            accent-color: var(--teal);
            cursor: pointer;
            flex-shrink: 0;
        }
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 4px 0;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        .auth-divider span {
            font-size: 12px;
            color: #9ca3af;
            font-weight: 500;
        }
        @media (max-width: 575px) {
            .auth-card {
                padding: 28px 20px;
            }
        }
    </style>
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
                    class="lg:hidden border-0 shadow-none p-2 focus:outline-none">
                    <span class="fa fa-bars text-[--teal] text-xl"></span>
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
                class="lg:hidden pb-4">
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
    <main class="w-full px-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </main>
    <!-- Footer -->
    <div class="w-full bg-[--text-dark] pt-12">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pb-8">
                <div class="lg:col-span-7">
                    <h1 class="text-white text-2xl font-semibold mb-0">Subscribe our newsletter</h1>
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
                    <h4 class="text-white font-semibold mb-4">Latest Post</h4>
                    <div class="flex border-b border-gray-700 py-4">
                        <img src="{{ asset('img/blog-mini-1.jpg')}}" class="shrink-0 w-16 h-16 object-cover" alt="">
                        <div class="pl-3">
                            <p class="mb-0 text-gray-500 text-sm">01 Jan 2045</p>
                            <a href="" class="text-gray-300 hover:text-white">Lorem ipsum dolor sit amet elit eros vel</a>
                        </div>
                    </div>
                    <div class="flex py-4">
                        <img src="{{ asset('img/blog-mini-2.jpg')}}" class="shrink-0 w-16 h-16 object-cover" alt="">
                        <div class="pl-3">
                            <p class="mb-0 text-gray-500 text-sm">01 Jan 2045</p>
                            <a href="" class="text-gray-300 hover:text-white">Lorem ipsum dolor sit amet elit eros vel</a>
                        </div>
                    </div>
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
                            dataLayer.push({ event: 'newsletter_signup' });
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