<x-app-layout :title="$course->title" :description="Str::limit(strip_tags($course->description), 155)" :image="$course->thumbnail ? Storage::url($course->thumbnail) : null">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $course->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-gray-600">{{ $course->description }}</p>

                @if ($isEnrolled)
                {{-- Progress bar --}}
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-500 mb-1">
                        <span>Progress</span><span>{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
                @else
                @auth
                <form method="POST" action="{{ route('courses.enroll', $course) }}" class="mt-4">
                    @csrf
                    <button class="bg-pink-600 text-white px-5 py-2 rounded text-sm hover:bg-pink-700">
                        Enroll Now — It's Free
                    </button>
                </form>
                @else
                {{-- Guest CTA --}}
                <div class="mt-4 p-4 rounded-xl border-2 border-dashed border-teal-200 bg-teal-50 text-center">
                    <p class="text-teal-800 text-sm font-medium mb-3">
                        🔒 Create a free account to enroll in this course
                    </p>
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('register') }}" class="bg-teal-700 text-white text-sm px-5 py-2 rounded-lg font-medium hover:bg-teal-800">
                            Register Free
                        </a>
                        <a href="{{ route('login') }}" class="border border-teal-600 text-teal-700 text-sm px-5 py-2 rounded-lg font-medium hover:bg-teal-50">
                            Log In
                        </a>
                    </div>
                </div>
                @endauth
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Lessons</h3>
                <div class="space-y-2">
                    @foreach ($course->lessons as $lesson)
                    <div class="flex justify-between items-center border-b py-2 last:border-0">
                        <span class="text-sm text-gray-700">
                            {{ $loop->iteration }}. {{ $lesson->title }}
                            @if ($lesson->quiz)
                            <span class="text-xs text-gray-400">(has quiz)</span>
                            @endif
                        </span>
                        @if ($isEnrolled)
                        <div class="flex items-center gap-2">
                            @if ($lesson->isCompletedBy(auth()->user()) && (!$lesson->quiz || $lesson->quiz->isPassedBy(auth()->user())))
                            <span class="text-xs text-green-600">✅ Done</span>
                            @else
                            <a href="{{ route('lessons.show', $lesson) }}" class="text-sm text-pink-600 hover:underline">Continue →</a>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @if ($course->quiz && $isEnrolled)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-2">Final Course Quiz</h3>

                @if ($course->allLessonsCompletedAndPassedBy(auth()->user()))
                <a href="{{ route('quiz.show', $course) }}" class="inline-block bg-pink-600 text-white text-sm px-4 py-2 rounded hover:bg-pink-700">
                    {{ $course->quiz->isPassedBy(auth()->user()) ? '✅ Retake Final Quiz' : 'Take Final Quiz' }}
                </a>
                @else
                <button disabled class="inline-block bg-gray-200 text-gray-500 text-sm px-4 py-2 rounded cursor-not-allowed">
                    🔒 Complete all lessons & quizzes to unlock
                </button>
                @endif
            </div>
            @endif
            @if (auth()->check() && $course->isCertificateEligibleFor(auth()->user()))
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-2">🎓 Certificate</h3>
                <p class="text-sm text-gray-500 mb-3">You've completed all requirements for this course!</p>
                <form method="POST" action="{{ route('certificate.generate', $course) }}">
                    @csrf
                    <button class="bg-green-600 text-white text-sm px-4 py-2 rounded hover:bg-green-700">Get Certificate</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>