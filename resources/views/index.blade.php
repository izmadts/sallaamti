{{-- resources/views/index.blade.php — Facebook-style landing: guests get an
     elegant hero (Banner-slider background) with a Sign In / Create Account
     tab card, plus a real-content community preview to drive registration
     (auth-check redirect to /dashboard for everyone else lives in
     routes/web.php). Full marketing content that used to live here has
     moved into resources/views/about.blade.php. --}}
<x-guest-layout :description="'Quran education, halal matrimonial matching, and community programs for the Muslim Ummah — ' . setting('site_tagline')">
    @section('title', 'Sallaamti — Learn Quran Online | Live Classes | Islamic Matrimonial')
    @section('description', 'Join Sallaamti to learn Quran online with expert teachers. Self-paced Quran courses, live classes, halal matrimonial platform and family counseling. Free to join.')
    @section('keywords', 'learn quran online pakistan, online quran classes, quran teacher online, islamic matrimonial pakistan, nikah platform')
    @section('og_title', 'Sallaamti — Learn Quran Online | Live Classes | Islamic Matrimonial')
    @section('og_description', 'Learn Quran with expert teachers. Join thousands of Muslims worldwide.')
    @section('og_image', asset('img/og-home.jpg'))

    @push('schema')
    @php
    $websiteSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'Sallaamti',
    'url' => config('app.url'),
    'potentialAction' => [
    '@type' => 'SearchAction',
    'target' => config('app.url') . '/courses?search={search_term_string}',
    'query-input' => 'required name=search_term_string',
    ],
    ];
    @endphp
    <script type="application/ld+json">
        {
            !!json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!
        }
    </script>
    @endpush

    {{-- ============================================================ --}}
    {{-- HERO — elegant Banner-image slider background behind a          --}}
    {{-- Sign In / Create Account tab card, so a guest can convert       --}}
    {{-- without ever leaving this page.                                --}}
    {{-- ============================================================ --}}
    <section class="relative overflow-hidden" x-data="{ tab: 'login', active: 0 }"
        @if ($banners->count() > 1)
        x-init="setInterval(() => active = (active + 1) % {{ $banners->count() }}, 6000)"
        @endif
        style="min-height: calc(100vh - 64px)">

        {{-- Slider background --}}
        <div class="absolute inset-0">
            @forelse ($banners as $i => $banner)
            @php $bannerUrl = str_starts_with($banner->image, 'img/') ? asset($banner->image) : Storage::url($banner->image); @endphp
            <div class="absolute inset-0 bg-cover bg-center transition-opacity ease-in-out"
                style="background-image: url('{{ $bannerUrl }}'); transition-duration: 1500ms; opacity: {{ $i === 0 ? 1 : 0 }}"
                :style="'background-image: url(' + @js($bannerUrl) + '); opacity: ' + (active === {{ $i }} ? 1 : 0)"></div>
            @empty
            <div class="absolute inset-0" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)"></div>
            @endforelse
            {{-- Dark teal/gold gradient overlay so white text stays legible over any photo --}}
            <div class="absolute inset-0" style="background: linear-gradient(120deg, rgba(9,50,50,.94) 0%, rgba(13,79,79,.88) 55%, rgba(184,150,46,.55) 100%)"></div>
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-4 py-10 sm:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">

                {{-- Tab card FIRST on mobile — the primary action should be
                     immediately visible. --}}
                <div class="order-1 lg:order-2">
                    <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 max-w-md mx-auto lg:mx-0 lg:ml-auto">

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

                        <div x-show="tab === 'login'">
                            <div class="text-center mb-5">
                                <x-application-logo class="h-11 w-auto mx-auto" />
                                <h1 class="text-lg font-bold text-gray-800 mt-2">{{ __('db.Welcome Back') }}</h1>
                                <p class="text-gray-500 text-xs mt-1">{{ __('db.Sign in to continue') }}</p>
                            </div>
                            @include('auth.partials.login-form')
                        </div>

                        <div x-show="tab === 'register'" x-cloak>
                            <div class="text-center mb-5">
                                <x-application-logo class="h-11 w-auto mx-auto" />
                                <h1 class="text-lg font-bold text-gray-800 mt-2">{{ __('db.Join Sallaamti Free') }}</h1>
                                <p class="text-gray-500 text-xs mt-1">{{ __('db.Takes less than 2 minutes') }}</p>
                            </div>
                            @include('auth.partials.register-form')
                        </div>

                    </div>
                </div>

                {{-- Pitch — real, keyword-natural content, not just a logo, so
                     the root domain keeps the SEO substance the old homepage had. --}}
                <div class="order-2 lg:order-1 text-center lg:text-left text-white">
                    <x-application-logo class="h-14 sm:h-16 w-auto mx-auto lg:mx-0 fill-current text-white mb-4" />
                    <p class="text-sm font-semibold tracking-widest" style="color: var(--gold)">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
                    <h2 class="text-3xl sm:text-4xl font-extrabold mt-2 leading-tight">
                        {{ __('db.Learn Quran Online. Find Your Match. Build Community.') }}
                    </h2>
                    <p class="text-white/80 mt-4 leading-relaxed">
                        {{ __('db.Sallaamti brings self-paced Quran courses, live classes with qualified teachers, a halal Islamic matrimonial platform, and family counseling together in one place — built for Muslims everywhere, free to join.') }}
                    </p>

                    <div class="flex justify-center lg:justify-start gap-4 flex-wrap mt-6">
                        @foreach (['Free to Join', 'Quran Courses', 'Nikah Platform', 'Live Classes'] as $t)
                        <span class="text-xs font-medium flex items-center gap-1 text-white/90">
                            <i class="fa fa-check-circle" style="color: var(--gold)"></i> {{ __("db.$t") }}
                        </span>
                        @endforeach
                    </div>

                    <p class="text-sm text-white/70 mt-8">
                        {{ __('db.Want to know more about our mission first?') }}
                        <a href="{{ url('/about') }}" class="font-semibold underline text-white">{{ __('db.Read our story') }} →</a>
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- CONVERSION — a real preview of the Sallaamti Wall community,   --}}
    {{-- so a guest sees genuine content (not a locked teaser) before   --}}
    {{-- the ask to register.                                          --}}
    {{-- ============================================================ --}}
    @if ($latestDuas->isNotEmpty())
    <section class="py-14" style="background: #fbfaf7">
        <div class="max-w-2xl mx-auto px-4">
            <div class="text-center mb-8">
                <span class="text-3xl">🤲</span>
                <h2 class="text-2xl font-bold text-gray-800 mt-2">{{ __('db.Join a community that prays for each other') }}</h2>
                <p class="text-gray-500 text-sm mt-1">{{ __('db.A glimpse of the Sallaamti Wall — real duas from real members.') }}</p>
            </div>

            <div class="space-y-4">
                @foreach ($latestDuas as $dua)
                @include('dua-wall.partials.dua-card', ['dua' => $dua])
                @endforeach
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('register') }}" class="btn-base btn-teal inline-flex items-center px-6 py-3 text-base font-semibold">
                    {{ __('db.Join Free & Share Your Own Dua') }} <i class="fa fa-arrow-right ms-2"></i>
                </a>
                <p class="text-xs text-gray-400 mt-3">
                    <a href="{{ route('wall.index') }}" class="underline" style="color: var(--teal)">{{ __('db.See the full Sallaamti Wall') }} →</a>
                </p>
            </div>
        </div>
    </section>
    @endif

    <section class="py-10 bg-cream">
        <div class="max-w-2xl mx-auto px-4">
            @include('components.daily-content-widget')
        </div>
    </section>
</x-guest-layout>
