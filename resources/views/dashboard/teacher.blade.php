<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Teacher Panel — {{ $user->name }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-purple-400">
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['total_courses'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">My Courses</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-teal-400">
                    <p class="text-2xl font-bold text-teal-600">{{ $stats['total_students'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Active Students</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-{{ $stats['todays_links_posted'] > 0 ? 'green' : 'red' }}-400">
                    <p class="text-2xl font-bold text-{{ $stats['todays_links_posted'] > 0 ? 'green' : 'red' }}-600">
                        {{ $stats['todays_links_posted'] > 0 ? '✅' : '❌' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Today's Link Posted</p>
                </div>
            </div>

            {{-- My Courses --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">My Assigned Courses</h3>
                @forelse ($courses as $course)
                <div class="flex justify-between items-center py-3 border-b last:border-0">
                    <div>
                        <p class="font-medium text-gray-800">{{ $course->title }}</p>
                        <p class="text-xs text-gray-500">{{ $course->class_time }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('teacher.courses.show', $course) }}" class="text-sm bg-purple-600 text-white px-3 py-1.5 rounded hover:bg-purple-700">
                            {{ $course->todaysLink() ? 'Update Link' : 'Post Today\'s Link' }}
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No courses assigned yet. Contact admin.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>