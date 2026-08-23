{{-- resources/views/index.blade.php — guests land on a banner-slider hero
     with a short pitch and the module card grid; tapping a card carries
     that module through registration (see ModuleRedirects) straight into
     its own first step (auth-check redirect to /dashboard for everyone
     else lives in routes/web.php). Full marketing content that used to
     live here has moved into resources/views/about.blade.php. --}}
<x-guest-layout :description="'Quran education, halal matrimonial matching, and community programs for the Muslim Ummah — ' . setting('site_tagline')">
    @section('title', 'Sallaamti — Learn Quran Online | Live Classes | Islamic Matrimonial')
    @section('description', 'Join Sallaamti to learn Quran online with expert teachers. Self-paced Quran courses, live classes, free Digital Skills training, halal matrimonial platform and family counseling. Free to join.')
    @section('keywords', 'learn quran online pakistan, online quran classes, quran teacher online, islamic matrimonial pakistan, nikah platform, free digital skills courses, web development graphic design digital marketing courses')
    @section('og_title', 'Sallaamti — Learn Quran Online | Live Classes | Islamic Matrimonial')
    @section('og_description', 'Learn Quran with expert teachers, free Digital Skills courses, and a halal matrimonial platform. Join thousands of Muslims worldwide.')
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
    {{-- HERO — banner slider background behind a short pitch AND the   --}}
    {{-- module card grid, in the same section (no login/register form  --}}
    {{-- here anymore, no scroll-jump to a second section either — tap  --}}
    {{-- a card and register/login carries that module through, landing --}}
    {{-- you straight in its first step. See ModuleRedirects +          --}}
    {{-- RegisteredUserController/AuthenticatedSessionController for    --}}
    {{-- how ?module= on the register/login page resolves after auth.   --}}
    {{-- ============================================================ --}}
    <section class="relative overflow-hidden" x-data="{ active: 0 }"
        @if ($banners->count() > 1)
        x-init="setInterval(() => active = (active + 1) % {{ $banners->count() }}, 6000)"
        @endif>

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

        <div class="relative z-10 max-w-6xl mx-auto px-4 py-14 sm:py-20">
            <div class="max-w-2xl mx-auto text-center text-white">
                <p class="text-sm font-semibold tracking-widest" style="color: var(--gold)">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
                <h1 class="text-3xl sm:text-4xl font-extrabold mt-2 leading-tight">
                    {{ __('db.Learn Quran. Find Your Match. Build Community.') }}
                </h1>
                <p class="text-white/80 mt-4 leading-relaxed">
                    {{ __('db.Sallaamti brings self-paced Quran courses, live classes with qualified teachers, free Digital Skills training, a halal Islamic matrimonial platform, and family counseling together in one place — built for Muslims everywhere, free to join.') }}
                </p>
                <p class="text-white/70 text-sm font-semibold mt-6">{{ __('db.Tap a card below to get started — we\'ll walk you through it step by step.') }}</p>
            </div>

            @php
            $modules = [
            ['key' => 'nikah', 'emoji' => '💍', 'color' => '#B8455A', 'title' => __('db.Nikah'), 'tagline' => __('db.Find a halal match')],
            ['key' => 'quran', 'emoji' => '📖', 'color' => '#0D6B6B', 'title' => __('db.Quran Courses'), 'tagline' => __('db.Nazrah to Tajweed')],
            ['key' => 'quran_live', 'emoji' => '🎥', 'color' => '#1D5FB8', 'title' => __('db.Live Classes'), 'tagline' => __('db.1-to-1 with a teacher')],
            ['key' => 'skills', 'emoji' => '💻', 'color' => '#6D4AAE', 'title' => __('db.Digital Skills'), 'tagline' => __('db.Free, self-paced')],
            ['key' => 'counseling', 'emoji' => '💑', 'color' => '#2E8B8B', 'title' => __('db.Family Support'), 'tagline' => __('db.Confidential counseling')],
            ['key' => 'donation', 'emoji' => '💝', 'color' => '#B8962E', 'title' => __('db.Donate'), 'tagline' => __('db.Fund a student')],
            ['key' => 'volunteer', 'emoji' => '🤝', 'color' => '#D2691E', 'title' => __('db.Volunteer'), 'tagline' => __('db.Give your skills')],
            ['key' => 'wall', 'emoji' => '🤲', 'color' => '#0D6B6B', 'title' => __('db.Sallaamti Wall'), 'tagline' => __('db.Duas & community')],
            ];
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5 mt-10">
                @foreach ($modules as $m)
                <a href="{{ auth()->check() ? (\App\Support\ModuleRedirects::resolve($m['key']) ?? route('dashboard')) : route('register', ['module' => $m['key']]) }}"
                    class="group relative flex flex-col items-center text-center rounded-2xl p-5 sm:p-6 transition-all duration-200 hover:-translate-y-1 hover:shadow-xl"
                    style="background: rgba(255,255,255,0.92); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 2px solid {{ $m['color'] }}">
                    <span class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mb-3 transition-transform duration-200 group-hover:scale-110"
                        style="background: {{ $m['color'] }}">
                        {{ $m['emoji'] }}
                    </span>
                    <span class="font-bold text-sm sm:text-base" style="color: {{ $m['color'] }}">{{ $m['title'] }}</span>
                    <span class="text-xs text-gray-600 mt-0.5">{{ $m['tagline'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- SHORT ABOUT — a quick pitch, admin-editable via Settings >     --}}
    {{-- About, with a link out to the full story.                     --}}
    {{-- ============================================================ --}}
    <section class="py-16 sm:py-20" style="background: var(--teal-light)">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <span class="section-eyebrow">{{ setting('about_heading') ?: __('db.About Sallaamti') }}</span>
            <p class="text-gray-700 text-base sm:text-lg leading-relaxed mt-3">
                {{ setting('about_text') ?: __('db.Sallaamti brings Quran education, a halal matrimonial platform, family counseling, and community giving together in one trusted place — built for Muslims everywhere, and free to join.') }}
            </p>
            <a href="{{ url('/about') }}" class="btn-base btn-teal inline-flex items-center px-6 py-3 text-sm font-semibold mt-6">
                {{ __('db.Read Our Full Story') }} <i class="fa fa-arrow-right ms-2"></i>
            </a>
        </div>
    </section>
</x-guest-layout>
