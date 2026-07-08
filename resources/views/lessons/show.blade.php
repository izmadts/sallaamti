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
                @if (session('status'))
                <div class="p-3 bg-green-50 text-green-700 rounded mb-4 text-sm">{{ session('status') }}</div>
                @endif

                <h3 class="font-semibold text-xl mb-4">{{ $lesson->title }}</h3>

                @if ($lesson->video_url)
                <div class="aspect-video mb-4">
                    <iframe src="{{ $lesson->embed_url }}" class="w-10 h-full rounded" allowfullscreen></iframe>
                </div>
                @endif

                <div class="prose text-gray-700">
                    {!! $lesson->content !!}
                </div>

                @if ($lesson->file_path)
                <div class="mt-4">
                    <a href="{{ Storage::url($lesson->file_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded hover:bg-gray-200">
                        📎 {{ $lesson->file_name ?? 'Download lesson file' }}
                    </a>
                </div>
                @endif

                <div class="mt-6">
                    @if ($isCompleted)
                    <span class="text-green-600 text-sm font-medium">✅ Completed</span>
                    @else
                    <form method="POST" action="{{ route('lessons.complete', $lesson) }}">
                        @csrf
                        <button class="bg-green-600 text-white px-5 py-2 rounded text-sm hover:bg-green-700">Mark as Complete</button>
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