<x-guest-layout>

    {{-- ============================================================ --}}
    {{-- PAGE HERO --}}
    {{-- ============================================================ --}}
    <section class="page-hero relative overflow-hidden flex items-center"
        style="min-height: 280px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-5 flex items-center justify-center"
            style="font-size: 18rem; color: #fff;">💻</div>
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 w-full">
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div>
                    <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">{{ __('db.Presented by IZMA Digital Technology & Security') }}</span>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">
                        {{ __('db.Digital Skills Training') }}
                    </h1>
                    <p class="text-white/70 text-lg max-w-xl">
                        {{ __('db.Practical, job-ready skills for a dignified livelihood — Web Development, Graphic Design, Digital Marketing, Mobile Apps & Computer Skills. Learn at your own pace, earn a certificate.') }}
                    </p>
                </div>
                <div class="hidden lg:flex justify-end gap-4">
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-center text-white">
                        <div class="text-3xl font-extrabold" style="color: #b8962e">{{ \App\Models\Course::where('is_published',true)->where('track','skills')->count() }}+</div>
                        <div class="text-xs text-white/70 mt-1 uppercase tracking-wider">{{ __('db.Courses') }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-center text-white">
                        <div class="text-3xl font-extrabold" style="color: #b8962e">{{ \App\Models\Enrollment::whereHas('course', fn($q) => $q->where('track','skills'))->count() }}+</div>
                        <div class="text-xs text-white/70 mt-1 uppercase tracking-wider">{{ __('db.Enrollments') }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-center text-white">
                        <div class="text-3xl font-extrabold" style="color: #b8962e">{{ \App\Models\Certificate::whereHas('course', fn($q) => $q->where('track','skills'))->count() }}+</div>
                        <div class="text-xs text-white/70 mt-1 uppercase tracking-wider">{{ __('db.Certificates') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- IZMA CREDIT + MODULE QUICK NAV --}}
    {{-- ============================================================ --}}
    <section class="bg-cream pt-6">
        <div class="max-w-7xl mx-auto px-4 space-y-4">
            <x-izma-credit />
            <x-module-nav module="skills" layout="bar" />
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- CATEGORY FILTERS --}}
    {{-- ============================================================ --}}
    <section class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex overflow-x-auto gap-2 py-3 scrollbar-hide">
                <a href="{{ route('skills.index') }}"
                    class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 border-2
                {{ !request('category') ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 hover:border-teal-500 hover:text-teal-700' }}"
                    style="{{ !request('category') ? 'background: #0d6b6b; border-color: #0d6b6b' : '' }}">
                    🌟 {{ __('db.All Skills') }}
                </a>
                @foreach ($categories as $cat)
                @php
                $icons = [
                'Computer Skills' => '💻', 'Graphic Design' => '🎨', 'Web Development' => '🌐',
                'Mobile Apps' => '📱', 'Digital Marketing' => '📈',
                ];
                $icon = $icons[$cat] ?? '💡';
                $active = request('category') === $cat;
                @endphp
                <a href="{{ route('skills.index', ['category' => $cat]) }}"
                    class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 border-2
                {{ $active ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 hover:border-teal-500 hover:text-teal-700' }}"
                    style="{{ $active ? 'background: #0d6b6b; border-color: #0d6b6b' : '' }}">
                    {{ $icon }} {{ $cat }}
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- COURSES GRID --}}
    {{-- ============================================================ --}}
    <section class="py-12 bg-cream">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Results bar --}}
            <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
                <p class="text-sm text-gray-500">
                    {{ __('db.Showing') }} <strong class="text-gray-800">{{ $courses->total() }}</strong> {{ __('db.course(s)') }}
                    @if (request('category'))
                    {{ __('db.in') }} <strong class="text-gray-800">{{ request('category') }}</strong>
                    <a href="{{ route('skills.index') }}" class="ml-2 text-xs text-red-400 hover:text-red-600">✕ {{ __('db.Clear filter') }}</a>
                    @endif
                </p>
                @guest
                <a href="{{ route('register') }}"
                    class="btn-base btn-gold text-sm px-5 py-2 font-semibold">
                    {{ __('db.Join Free to Enroll →') }}
                </a>
                @endguest
            </div>

            {{-- Grid --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($courses as $course)

                @php
                $isEnrolled = auth()->check() && $course->isEnrolledBy(auth()->user());
                $progress = $isEnrolled ? $course->progressFor(auth()->user()) : 0;
                $categoryColors = [
                'Computer Skills' => '#0d6b6b', 'Graphic Design' => '#b8962e',
                'Web Development' => '#1a5276', 'Mobile Apps' => '#922b21',
                'Digital Marketing' => '#16a34a',
                ];
                $color = $categoryColors[$course->category] ?? '#0d6b6b';
                @endphp

                <div class="course-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col group">

                    {{-- Thumbnail --}}
                    <a href="{{ route('courses.show', $course) }}" class="block relative overflow-hidden">
                        @if ($course->thumbnail)
                        <img src="{{ Storage::url($course->thumbnail) }}"
                            class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-500"
                            alt="{{ $course->title }}">
                        @else
                        <div class="w-full h-44 flex items-center justify-center text-6xl"
                            style="background: linear-gradient(135deg, {{ $color }}22, {{ $color }}44)">
                            💻
                        </div>
                        @endif

                        {{-- Category badge --}}
                        <div class="absolute top-3 left-3">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white shadow-sm"
                                style="background: {{ $color }}">
                                {{ $course->category ?? __('db.Course') }}
                            </span>
                        </div>

                        {{-- Enrolled badge --}}
                        @if ($isEnrolled)
                        <div class="absolute top-3 right-3">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-500 text-white shadow-sm">
                                ✅ {{ __('db.Enrolled') }}
                            </span>
                        </div>
                        @endif
                    </a>

                    {{-- Body --}}
                    <div class="p-5 flex flex-col flex-1">

                        {{-- Title --}}
                        <a href="{{ route('courses.show', $course) }}"
                            class="block font-bold text-gray-800 text-base mb-1 leading-snug hover:text-teal-700 transition-colors line-clamp-2">
                            {{ $course->title }}
                        </a>

                        {{-- Description --}}
                        @if ($course->description)
                        <p class="text-xs text-gray-500 mb-3 leading-relaxed line-clamp-2">
                            {{ $course->description }}
                        </p>
                        @endif

                        {{-- Meta --}}
                        <div class="flex items-center gap-3 text-xs text-gray-400 mb-4 flex-wrap">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ $course->lessons_count }} lesson{{ $course->lessons_count !== 1 ? 's' : '' }}
                            </span>
                            @if ($course->level)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                {{ $course->level }}
                            </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                {{ $course->enrollments_count ?? 0 }} {{ __('db.enrolled') }}
                            </span>
                        </div>

                        {{-- Progress bar (enrolled users) --}}
                        @if ($isEnrolled)
                        <div class="mb-4">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-500">{{ __('db.Progress') }}</span>
                                <span class="font-semibold" style="color: #0d6b6b">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-1.5">
                                <div class="h-1.5 rounded-full transition-all"
                                    style="width: {{ $progress }}%; background: {{ $progress === 100 ? '#16a34a' : '#0d6b6b' }}">
                                </div>
                            </div>
                            <x-lesson-stars :total="$course->lessons_count" :completed="$course->completedLessonsCountFor(auth()->user())" />
                            @if ($progress === 100)
                            <p class="text-xs text-green-600 mt-1 font-semibold">🎉 {{ __('db.Completed! Get your certificate.') }}</p>
                            @endif
                        </div>
                        @endif

                        {{-- Spacer --}}
                        <div class="flex-1"></div>

                        {{-- CTA --}}
                        @if ($isEnrolled)
                        <a href="{{ route('courses.show', $course) }}"
                            class="block text-center py-2.5 rounded-xl text-sm font-semibold text-white transition-all"
                            style="background: {{ $progress === 100 ? '#16a34a' : '#0d6b6b' }}">
                            {{ $progress === 100 ? '🏆 '.__('db.Get Certificate') : '▶ '.__('db.Continue Learning') }}
                        </a>
                        @else
                        @auth
                        <a href="{{ route('courses.show', $course) }}"
                            class="block text-center py-2.5 rounded-xl text-sm font-semibold transition-all border-2"
                            style="border-color: #0d6b6b; color: #0d6b6b">
                            {{ __('db.Enroll Now — Free') }}
                        </a>
                        @else
                        <a href="{{ route('register') }}"
                            class="block text-center py-2.5 rounded-xl text-sm font-semibold text-white transition-all"
                            style="background: #b8962e">
                            {{ __('db.Register to Enroll') }}
                        </a>
                        @endauth
                        @endif

                    </div>
                </div>

                @empty
                <div class="col-span-full py-20 text-center">
                    <div class="text-6xl mb-4">💻</div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">{{ __('db.No Courses Found') }}</h3>
                    <p class="text-gray-400 mb-6">
                        @if (request('category'))
                        {{ __('db.No courses in the') }} <strong>{{ request('category') }}</strong> {{ __('db.category yet.') }}
                        @else
                        {{ __('db.No courses published yet. Check back soon!') }}
                        @endif
                    </p>
                    @if (request('category'))
                    <a href="{{ route('skills.index') }}" class="btn-base btn-teal px-6 py-2 text-sm">
                        {{ __('db.View All Skills') }}
                    </a>
                    @endif
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
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
            <div class="text-4xl mb-3">💼</div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-3">
                {{ __('db.Ready to Build a New Skill?') }}
            </h2>
            <p class="text-white/70 mb-6">
                {{ __('db.Create a free account and start learning practical, job-ready digital skills — presented by IZMA Digital Technology & Security.') }}
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

    {{-- Page-specific styles --}}
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

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

</x-guest-layout>
