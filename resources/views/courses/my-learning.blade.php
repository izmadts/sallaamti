<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Learning</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($courses as $item)
            <a href="{{ route('courses.show', $item['course']) }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md transition p-5">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-semibold text-gray-800">{{ $item['course']->title }}</h4>
                    <span class="text-sm text-gray-500">{{ $item['progress'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $item['progress'] }}%"></div>
                </div>
            </a>
            @empty
            <p class="text-gray-500">You haven't enrolled in any courses yet. <a href="{{ route('courses.index') }}" class="text-pink-600 underline">Browse courses</a></p>
            @endforelse
        </div>
    </div>
</x-app-layout>