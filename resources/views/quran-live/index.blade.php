<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Live Quran Classes (Monthly)</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($courses as $course)
            <a href="{{ route('quran-live.show', $course) }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md transition p-5 border border-gray-100">
                <h4 class="font-semibold text-gray-800">{{ $course->title }}</h4>
                <p class="text-sm text-gray-500 mt-1">Teacher: {{ $course->teacher?->name ?? 'TBA' }}</p>
                <p class="text-sm text-gray-500">{{ $course->class_time }}</p>
                <p class="text-sm font-medium text-pink-600 mt-2">Rs. {{ number_format($course->monthly_fee) }}/month</p>
            </a>
            @empty
            <p class="text-gray-500 col-span-full">No live courses available right now.</p>
            @endforelse
        </div>
        {{ $courses->links() }}
    </div>
</x-app-layout>