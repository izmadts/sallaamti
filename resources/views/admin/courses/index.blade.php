<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Manage Courses</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            <a href="{{ route('admin.courses.create') }}" class="inline-block bg-gray-800 text-white text-sm px-4 py-2 rounded">+ New Course</a>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @foreach ($courses as $course)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $course->title }} <span class="text-xs text-gray-400">({{ $course->lessons_count }} lessons, {{ $course->enrollments_count }} enrolled)</span></p>
                        <p class="text-xs {{ $course->is_published ? 'text-green-600' : 'text-yellow-600' }}">{{ $course->is_published ? 'Published' : 'Draft' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.courses.show', $course) }}" class="text-sm text-blue-600">Manage Lessons</a>
                        <a href="{{ route('admin.courses.edit', $course) }}" class="text-sm text-gray-600">Edit</a>
                    </div>
                </div>
                @endforeach
            </div>
            {{ $courses->links() }}
        </div>
    </div>
</x-admin-layout>