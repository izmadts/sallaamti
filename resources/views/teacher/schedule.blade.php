<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('db.My Weekly Schedule') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
            @php $dayGroups = $groups->filter(fn($g) => in_array($day, $g->class_days ?? [])); @endphp
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-semibold text-gray-700 mb-2">{{ $day }}</h3>
                @forelse ($dayGroups as $group)
                <div class="flex justify-between items-center border rounded p-3 mb-2">
                    <div>
                        <p class="font-medium text-sm">{{ $group->course->title }} — {{ $group->group_name }}</p>
                        <p class="text-xs text-gray-500">{{ $group->class_time }} | {{ $group->activeStudents->count() }} {{ __('db.students') }}</p>
                    </div>
                    <a href="{{ route('teacher.groups.show', $group) }}" class="text-xs text-teal-600">{{ __('db.Open') }} →</a>
                </div>
                @empty
                <p class="text-xs text-gray-400">{{ __('db.No class this day') }}</p>
                @endforelse
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>