<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Manage Courses</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="flex justify-between items-center flex-wrap gap-3">
                <a href="{{ route('admin.courses.create') }}" class="inline-block bg-gray-800 text-white text-sm px-4 py-2 rounded">+ New Course</a>
                <div class="flex gap-2 text-sm">
                    <a href="{{ route('admin.courses.index') }}" class="px-3 py-1.5 rounded-full border-2 {{ !request('track') ? 'border-teal-600 bg-teal-50 text-teal-700 font-semibold' : 'border-gray-200 text-gray-500' }}">All</a>
                    <a href="{{ route('admin.courses.index', ['track' => 'quran']) }}" class="px-3 py-1.5 rounded-full border-2 {{ request('track') === 'quran' ? 'border-teal-600 bg-teal-50 text-teal-700 font-semibold' : 'border-gray-200 text-gray-500' }}">📖 Quran</a>
                    <a href="{{ route('admin.courses.index', ['track' => 'skills']) }}" class="px-3 py-1.5 rounded-full border-2 {{ request('track') === 'skills' ? 'border-teal-600 bg-teal-50 text-teal-700 font-semibold' : 'border-gray-200 text-gray-500' }}">💻 Skills</a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($courses as $course)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="font-medium">
                            {{ $course->title }}
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $course->track === 'skills' ? 'bg-teal-100 text-teal-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $course->track === 'skills' ? '💻 Skills' : '📖 Quran' }}
                            </span>
                            <span class="text-xs text-gray-400">({{ $course->lessons_count }} lessons, {{ $course->enrollments_count }} enrolled)</span>
                        </p>
                        <p class="text-xs {{ $course->is_published ? 'text-green-600' : 'text-yellow-600' }}">{{ $course->is_published ? 'Published' : 'Draft' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.courses.show', $course) }}" class="text-sm text-blue-600">Manage Lessons</a>
                        <a href="{{ route('admin.courses.edit', $course) }}" class="text-sm text-gray-600">Edit</a>
                    </div>
                </div>
                @empty
                <p class="p-6 text-center text-sm text-gray-400">No courses{{ request('track') ? ' in this track' : '' }} yet.</p>
                @endforelse
            </div>
            {{ $courses->links() }}
        </div>
    </div>
</x-admin-layout>