<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $course->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- Sidebar -->
            <div class="bg-white rounded-lg shadow-sm p-4 lg:col-span-1">
                <h4 class="font-semibold text-sm text-gray-500 mb-2">Lessons</h4>
                @foreach ($lessons as $l)
                <a href="{{ route('lessons.show', $l) }}" class="block text-sm py-1.5 {{ $l->id === $lesson->id ? 'text-pink-600 font-medium' : 'text-gray-600' }}">
                    {{ $l->isCompletedBy(auth()->user()) ? '✅' : '○' }} {{ $l->title }}
                </a>
                @endforeach
            </div>

            <!-- Content -->
            <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-3">

                <p class="text-xs font-semibold text-teal-600 uppercase tracking-wide mb-1">
                    ⭐ Lesson {{ $lessons->search(fn($l) => $l->id === $lesson->id) + 1 }} of {{ $lessons->count() }}
                </p>
                <div class="flex items-start justify-between gap-3 mb-4">
                    <h3 class="font-semibold text-xl">{{ $lesson->title }}</h3>
                    <button type="button" onclick="speakLessonText()"
                        class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-lg hover:bg-teal-50" style="background: var(--teal-light)" title="Read aloud">
                        🔊
                    </button>
                </div>

                @if ($lesson->video_url)
                <div class="aspect-video mb-4 rounded-lg overflow-hidden bg-black">
                    <iframe src="{{ $lesson->embed_url }}" class="w-full h-full" allowfullscreen></iframe>
                </div>
                @endif

                <div class="prose text-gray-700" id="lesson-content">
                    {!! $lesson->content !!}
                </div>

                <script>
                    function speakLessonText() {
                        if (!('speechSynthesis' in window)) return;
                        window.speechSynthesis.cancel();
                        const text = document.getElementById('lesson-content').innerText;
                        window.speechSynthesis.speak(new SpeechSynthesisUtterance(text));
                    }
                </script>

                @if ($lesson->file_path)
                <div class="mt-4">
                    <a href="{{ Storage::url($lesson->file_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded hover:bg-gray-200">
                        📎 {{ $lesson->file_name ?? 'Download lesson file' }}
                    </a>
                </div>
                @endif

                <div class="mt-6">
                    @if ($isCompleted)
                    <div class="rounded-xl p-3 inline-flex items-center gap-2" style="background: #f0fdf4; border: 1px solid #86efac">
                        <span class="text-xl" style="animation: celebrate-bounce 0.6s ease">⭐</span>
                        <span class="text-green-700 text-sm font-bold">Lesson complete — great job!</span>
                    </div>
                    @else
                    <form method="POST" action="{{ route('lessons.complete', $lesson) }}"
                        x-data="{ remaining: {{ (int) $secondsRemaining }} }"
                        x-init="remaining > 0 && setInterval(() => remaining > 0 && remaining--, 1000)">
                        @csrf
                        <button type="submit" :disabled="remaining > 0"
                            class="bg-green-600 text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                            <span x-show="remaining === 0">⭐ Mark as Complete</span>
                            <span x-show="remaining > 0" x-text="'Review the lesson (' + remaining + 's)'"></span>
                        </button>
                    </form>
                    @endif
                </div>
                @if ($lesson->quiz)
                <div class="mt-4">
                    <a href="{{ route('lesson.quiz.show', $lesson) }}" class="inline-block bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700">
                        {{ $lesson->quiz->isPassedBy(auth()->user()) ? '✅ Retake Lesson Quiz' : '📝 Take Lesson Quiz' }}
                    </a>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>