<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $course->title }} — Lessons</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <a href="{{ route('admin.courses.lessons.create', $course) }}" class="inline-block bg-gray-800 text-white text-sm px-4 py-2 rounded">+ Add Lesson</a>
            <a href="{{ route('admin.courses.quiz.edit', $course) }}" class="inline-block bg-blue-600 text-white text-sm px-4 py-2 rounded mb-4">Manage Quiz</a>
            <div class="bg-white rounded-lg shadow-sm divide-y">
                @foreach ($course->lessons as $lesson)
                <div class="p-4 flex justify-between items-center">
                    <span>{{ $lesson->order }}. {{ $lesson->title }}</span>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="text-sm text-blue-600">Edit</a>
                        <a href="{{ route('admin.lessons.quiz.edit', $lesson) }}" class="text-sm text-purple-600">Lesson Quiz</a>
                        <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('Delete this lesson?')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-red-600">Delete</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>