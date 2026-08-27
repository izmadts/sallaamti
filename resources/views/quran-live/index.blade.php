<x-guest-layout>

    {{-- ============================================================ --}}
    {{-- PAGE HERO --}}
    {{-- ============================================================ --}}
    <section class="page-hero relative overflow-hidden flex items-center"
        style="min-height: 220px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-5 flex items-center justify-center"
            style="font-size: 18rem; color: #fff;">🎥</div>
        <div class="max-w-7xl mx-auto px-4 py-12 relative z-10 w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">{{ __('db.Quran & Islamic Learning') }}</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">
                {{ __('db.Live Quran Classes') }}
            </h1>
            <p class="text-white/70 text-lg max-w-xl">
                {{ __('db.Learn face-to-face with a qualified teacher over live video — small groups, monthly subscription, real-time feedback.') }}
            </p>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- MODULE QUICK NAV --}}
    {{-- ============================================================ --}}
    <section class="bg-cream pt-6">
        <div class="max-w-7xl mx-auto px-4">
            <x-module-nav module="quran" layout="bar" />
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- CLASSES GRID --}}
    {{-- ============================================================ --}}
    <section class="py-10 bg-cream">
        <div class="max-w-7xl mx-auto px-4">

            <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
                <p class="text-sm text-gray-500">
                    {{ __('db.Showing') }} <strong class="text-gray-800">{{ $courses->total() }}</strong> {{ __('db.live class(es)') }}
                </p>
                @guest
                <a href="{{ route('register') }}" class="btn-base btn-gold text-sm px-5 py-2 font-semibold">
                    {{ __('db.Join Free to Apply →') }}
                </a>
                @endguest
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($courses as $course)
                @php
                $categoryColors = [
                'Nazrah' => '#0d6b6b', 'Tajweed' => '#b8962e',
                'Translation' => '#1a5276', 'Arabic Grammar' => '#922b21',
                'Seerah' => '#16a34a', 'Hadith' => '#7d3c98',
                ];
                $color = $categoryColors[$course->category] ?? '#0d6b6b';
                @endphp

                <a href="{{ route('quran-live.show', $course) }}"
                    class="course-card block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 group">

                    <div class="w-full h-32 flex items-center justify-center text-5xl"
                        style="background: linear-gradient(135deg, {{ $color }}22, {{ $color }}44)">
                        🎥
                    </div>

                    <div class="p-5">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white shadow-sm"
                            style="background: {{ $color }}">
                            {{ $course->category ?? __('db.Live Class') }}
                        </span>
                        @if ($course->min_age || $course->max_age)
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full ml-1" style="background: var(--teal-light); color: var(--teal)">
                            🎂 {{ $course->min_age ?? '0' }}{{ $course->max_age ? '–'.$course->max_age : '+' }}
                        </span>
                        @endif

                        <h4 class="font-bold text-gray-800 text-base mt-3 mb-1 leading-snug group-hover:text-teal-700 transition-colors line-clamp-2">
                            {{ $course->title }}
                        </h4>

                        @if ($course->description)
                        <p class="text-xs text-gray-500 mb-3 leading-relaxed line-clamp-2">
                            {{ Str::limit(strip_tags($course->description), 140) }}
                        </p>
                        @endif

                        <div class="text-xs text-gray-400 space-y-1 mb-4">
                            <p>👤 {{ __('db.Teacher') }}: {{ $course->teacher?->name ?? __('db.TBA') }}</p>
                            @if ($course->class_days || $course->class_time)
                            <p>🗓️ {{ is_array($course->class_days) ? implode(', ', $course->class_days) : $course->class_days }} {{ $course->class_time }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold" style="color: #b8962e">
                                Rs. {{ number_format($course->monthly_fee) }}<span class="text-xs font-normal text-gray-400">/{{ __('db.month') }}</span>
                            </span>
                            <span class="text-xs font-semibold" style="color: #0d6b6b">
                                {{ __('db.View Details') }} →
                            </span>
                        </div>
                    </div>
                </a>

                @empty
                <div class="col-span-full py-20 text-center">
                    <div class="text-6xl mb-4">🎥</div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">{{ __('db.No Live Classes Available') }}</h3>
                    <p class="text-gray-400">{{ __('db.Check back soon — new batches open regularly.') }}</p>
                </div>
                @endforelse
            </div>

            @if ($courses->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $courses->links() }}
            </div>
            @endif

        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- GUEST CTA BANNER --}}
    {{-- ============================================================ --}}
    @guest
    <section class="py-14 final-cta-section">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <div class="text-4xl mb-3">🎥</div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-3">
                {{ __('db.Ready to Learn Live?') }}
            </h2>
            <p class="text-white/70 mb-6">
                {{ __('db.Create a free account to apply for a live class and start learning with a real teacher.') }}
            </p>
            <div class="flex gap-3 justify-center flex-wrap">
                <a href="{{ route('register') }}" class="btn-base btn-gold px-8 py-3 font-semibold">
                    {{ __('db.Register Free') }} <i class="fa fa-arrow-right ml-2"></i>
                </a>
                <a href="{{ route('login') }}" class="btn-base btn-outline-light px-8 py-3">
                    {{ __('db.Log In') }}
                </a>
            </div>
        </div>
    </section>
    @endguest

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-4px);
        }
    </style>

    <x-faq-section module="quran_live" />
</x-guest-layout>
