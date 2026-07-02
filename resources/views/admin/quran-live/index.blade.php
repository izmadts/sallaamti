<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Manage Quran Live Courses</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))<div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>@endif
            <a href="{{ route('admin.quran-live-courses.create') }}" class="inline-block bg-gray-800 text-white text-sm px-4 py-2 rounded">+ New Course</a>
            <div class="bg-white rounded-lg shadow-sm divide-y">
                @foreach ($courses as $course)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $course->title }} <span class="text-xs text-gray-400">({{ $course->admissions_count }} admitted)</span></p>
                        <p class="text-xs text-gray-500">Teacher: {{ $course->teacher?->name ?? 'TBA' }} | Rs. {{ number_format($course->monthly_fee) }}/mo</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.quran-live-courses.subscriptions', $course) }}" class="text-sm text-blue-600">Subscriptions</a>
                        <a href="{{ route('admin.quran-live-courses.edit', $course) }}" class="text-sm text-gray-600">Edit</a>
                    </div>
                </div>
                @endforeach
            </div>
            {{ $courses->links() }}
        </div>
    </div>
</x-admin-layout>