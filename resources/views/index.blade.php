{{-- resources/views/index.blade.php — Facebook-style landing: guests get a
     split-screen pitch + login (auth-check redirect to /dashboard for
     everyone else lives in routes/web.php). Full marketing content that
     used to live here has moved into resources/views/about.blade.php. --}}
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

    <section class="py-8 sm:py-14" style="background: linear-gradient(135deg, #e8f5f5 0%, #fdfaf3 55%, #fdf6e3 100%); min-height: calc(100vh - 64px)">
        <div class="max-w-5xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">

                {{-- Sign-in card FIRST on mobile — the primary action should be
                     immediately visible, matching how a Facebook-style landing
                     leads with login rather than making a visitor scroll for it. --}}
                <div class="order-1 lg:order-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 max-w-md mx-auto lg:mx-0">
                        <div class="text-center mb-6">
                            <x-application-logo class="h-12 w-auto mx-auto" />
                            <h1 class="text-xl font-bold text-gray-800 mt-3">{{ __('db.Welcome to Sallaamti') }}</h1>
                            <p class="text-gray-500 text-sm mt-1">{{ __('db.Sign in to continue') }}</p>
                        </div>

                        @include('auth.partials.login-form')
                    </div>
                </div>

                {{-- Pitch — real, keyword-natural content, not just a logo, so
                     the root domain keeps the SEO substance the old homepage had. --}}
                <div class="order-2 lg:order-1 text-center lg:text-left">
                    <p class="text-sm font-semibold tracking-widest" style="color: var(--gold)">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mt-2 leading-tight">
                        {{ __('db.Learn Quran Online. Find Your Match. Build Community.') }}
                    </h2>
                    <p class="text-gray-600 mt-4 leading-relaxed">
                        {{ __('db.Sallaamti brings self-paced Quran courses, live classes with qualified teachers, a halal Islamic matrimonial platform, and family counseling together in one place — built for Muslims everywhere, free to join.') }}
                    </p>

                    <div class="flex justify-center lg:justify-start gap-4 flex-wrap mt-6">
                        @foreach (['Free to Join', 'Quran Courses', 'Nikah Platform', 'Live Classes'] as $t)
                        <span class="text-xs font-medium flex items-center gap-1" style="color: var(--teal)">
                            <i class="fa fa-check-circle" style="color: var(--gold)"></i> {{ __("db.$t") }}
                        </span>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('register') }}" class="btn-base btn-teal inline-flex items-center px-6 py-3 text-base font-semibold">
                            {{ __('db.Create Your Free Account') }} <i class="fa fa-arrow-right ms-2"></i>
                        </a>
                    </div>

                    <p class="text-sm text-gray-500 mt-6">
                        {{ __('db.Want to know more about our mission first?') }}
                        <a href="{{ url('/about') }}" class="font-semibold underline" style="color: var(--teal)">{{ __('db.Read our story') }} →</a>
                    </p>
                </div>

            </div>
        </div>
    </section>

    <section class="py-10 bg-cream">
        <div class="max-w-2xl mx-auto px-4">
            @include('components.daily-content-widget')
        </div>
    </section>
</x-guest-layout>
