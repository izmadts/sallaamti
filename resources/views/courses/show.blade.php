<x-app-layout :title="$course->title" :description="Str::limit(strip_tags($course->description), 155)" :image="$course->thumbnail ? Storage::url($course->thumbnail) : null">
    @php
    $courseDescription = Str::limit(strip_tags($course->description ?? 'Learn ' . $course->title . ' with Sallaamti — expert Quran education online.'), 160);
    $courseDescriptionLong = Str::limit(strip_tags($course->description ?? ''), 200);
    @endphp

    @section('title', $course->title . ' | Quran Course | Sallaamti')
    @section('description', $courseDescription)
    @section('canonical', route('courses.show', $course))
    @section('og_type', 'article')
    @section('og_title', $course->title . ' — Sallaamti Quran Course')
    @section('og_description', $courseDescription)
    @if ($course->thumbnail)
    @section('og_image', Storage::url($course->thumbnail))
    @endif

    @push('schema')
    @php
    $courseSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Course',
    'name' => $course->title,
    'description' => $courseDescriptionLong,
    'provider' => [
    '@type' => 'Organization',
    'name' => 'Sallaamti',
    'url' => config('app.url'),
    ],
    'url' => route('courses.show', $course),
    'courseMode' => 'online',
    'inLanguage' => 'ur',
    'isAccessibleForFree'=> true,
    'numberOfCredits' => $course->lessons_count ?? 0,
    'hasCourseInstance' => [
    '@type' => 'CourseInstance',
    'courseMode' => 'online',
    'instructor' => [
    '@type' => 'Person',
    'name' => 'Sallaamti Teacher',
    ],
    ],
    ];
    @endphp
    <script type="application/ld+json">
        {
            !!json_encode($courseSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!
        }
    </script>
    @endpush
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $course->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">


            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="prose prose-sm sm:prose-base max-w-none text-gray-600">{!! $course->description !!}</div>
                @if ($course->min_age || $course->max_age)
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full mt-2" style="background: var(--teal-light); color: var(--teal)">
                    🎂 {{ __('db.Ages') }} {{ $course->min_age ?? '0' }}{{ $course->max_age ? '–'.$course->max_age : '+' }}
                </span>
                @endif

                @if ($isEnrolled)
                {{-- Progress bar --}}
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-500 mb-1">
                        <span>{{ __('db.Progress') }}</span><span>{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                    <x-lesson-stars :total="$course->lessons->count()" :completed="$course->completedLessonsCountFor(auth()->user())" />
                </div>
                @else
                @auth
                <form method="POST" action="{{ route('courses.enroll', $course) }}" class="mt-4" x-data="{ ok: false }">
                    @csrf
                    <label class="flex items-start gap-2 text-sm text-gray-600 mb-3 cursor-pointer">
                        <input type="checkbox" x-model="ok" class="mt-0.5">
                        <span>{{ __('db.A parent/guardian is aware of and okay with this enrollment.') }}</span>
                    </label>
                    <button :disabled="!ok" class="bg-pink-600 text-white px-5 py-2 rounded text-sm hover:bg-pink-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __("db.Enroll Now — It's Free") }}
                    </button>
                </form>
                @else
                {{-- Guest CTA --}}
                <div class="mt-4 p-4 rounded-xl border-2 border-dashed border-teal-200 bg-teal-50 text-center">
                    <p class="text-teal-800 text-sm font-medium mb-3">
                        🔒 {{ __('db.Create a free account to enroll in this course') }}
                    </p>
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('register') }}" class="bg-teal-700 text-white text-sm px-5 py-2 rounded-lg font-medium hover:bg-teal-800">
                            {{ __('db.Register Free') }}
                        </a>
                        <a href="{{ route('login') }}" class="border border-teal-600 text-teal-700 text-sm px-5 py-2 rounded-lg font-medium hover:bg-teal-50">
                            {{ __('db.Log In') }}
                        </a>
                    </div>
                </div>
                @endauth
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.Lessons') }}</h3>
                <div class="space-y-2">
                    @foreach ($course->lessons as $lesson)
                    <div class="flex justify-between items-center border-b py-2 last:border-0">
                        <span class="text-sm text-gray-700">
                            {{ $loop->iteration }}. {{ $lesson->title }}
                            @if ($lesson->quiz)
                            <span class="text-xs text-gray-400">{{ __('db.(has quiz)') }}</span>
                            @endif
                        </span>
                        @if ($isEnrolled)
                        <div class="flex items-center gap-2">
                            @if ($lesson->isCompletedBy(auth()->user()) && (!$lesson->quiz || $lesson->quiz->isPassedBy(auth()->user())))
                            <span class="text-xs text-green-600">✅ {{ __('db.Done') }}</span>
                            @else
                            <a href="{{ route('lessons.show', $lesson) }}" class="text-sm text-pink-600 hover:underline">{{ __('db.Continue →') }}</a>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @if ($course->quiz && $isEnrolled)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-2">{{ __('db.Final Course Quiz') }}</h3>

                @if ($course->allLessonsCompletedAndPassedBy(auth()->user()))
                <a href="{{ route('quiz.show', $course) }}" class="inline-block bg-pink-600 text-white text-sm px-4 py-2 rounded hover:bg-pink-700">
                    {{ $course->quiz->isPassedBy(auth()->user()) ? '✅ '.__('db.Retake Final Quiz') : __('db.Take Final Quiz') }}
                </a>
                @else
                <button disabled class="inline-block bg-gray-200 text-gray-500 text-sm px-4 py-2 rounded cursor-not-allowed">
                    🔒 {{ __('db.Complete all lessons & quizzes to unlock') }}
                </button>
                @endif
            </div>
            @endif
            @if (auth()->check() && $course->isCertificateEligibleFor(auth()->user()))
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-2">🎓 {{ __('db.Certificate') }}</h3>
                @if (!$certificate)
                <p class="text-sm text-gray-500 mb-3">{{ __("db.You've completed all requirements for this course!") }}</p>
                <form method="POST" action="{{ route('certificate.generate', $course) }}">
                    @csrf
                    <button class="bg-green-600 text-white text-sm px-4 py-2 rounded hover:bg-green-700">{{ __('db.Request Certificate') }}</button>
                </form>
                @elseif ($certificate->isPending())
                <p class="text-sm text-amber-600">⏳ {{ __('db.Your certificate request is awaiting admin review.') }}</p>
                @elseif ($certificate->status === 'rejected')
                <p class="text-sm text-red-600 mb-1">❌ {{ __('db.Your certificate request wasn\'t approved.') }}</p>
                @if ($certificate->rejection_reason)
                <p class="text-sm text-gray-500 mb-3">{{ $certificate->rejection_reason }}</p>
                @endif
                <form method="POST" action="{{ route('certificate.generate', $course) }}">
                    @csrf
                    <button class="bg-green-600 text-white text-sm px-4 py-2 rounded hover:bg-green-700">{{ __('db.Request Again') }}</button>
                </form>
                @else
                <p class="text-sm text-gray-500 mb-3">{{ __('db.Your certificate is approved and ready.') }}</p>
                <a href="{{ route('certificate.download', $certificate) }}" class="inline-block bg-green-600 text-white text-sm px-4 py-2 rounded hover:bg-green-700">{{ __('db.Download Certificate') }}</a>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>