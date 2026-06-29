<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quran & Islamic Learning</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex gap-2 flex-wrap">
                <a href="{{ route('courses.index') }}" class="text-sm px-3 py-1.5 rounded-full {{ !request('category') ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600' }}">All</a>
                @foreach ($categories as $cat)
                <a href="{{ route('courses.index', ['category' => $cat]) }}" class="text-sm px-3 py-1.5 rounded-full {{ request('category') === $cat ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $cat }}</a>
                @endforeach
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($courses as $course)
                <a href="{{ route('courses.show', $course) }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100">
                    @if ($course->thumbnail)
                    <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-40 object-cover">
                    @else
                    <div class="w-full h-40 bg-gray-100 flex items-center justify-center text-4xl">📖</div>
                    @endif
                    <div class="p-4">
                        <p class="text-xs text-gray-400 uppercase">{{ $course->category }}</p>
                        <h4 class="font-semibold text-gray-800">{{ $course->title }}</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $course->lessons_count }} lessons</p>
                    </div>
                </a>
                @empty
                <p class="text-gray-500 col-span-full">No courses available yet.</p>
                @endforelse
            </div>
            {{ $courses->links() }}
        </div>
    </div>
</x-app-layout>