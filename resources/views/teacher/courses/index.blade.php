<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">My Courses</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($courses as $course)
            <a href="{{ route('teacher.courses.show', $course) }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md p-5">
                <h4 class="font-semibold">{{ $course->title }}</h4>
                <p class="text-sm text-gray-500">{{ $course->class_time }}</p>
            </a>
            @empty
            <p class="text-gray-500">No courses assigned to you yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>