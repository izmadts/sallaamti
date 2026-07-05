<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">My Class Groups</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($groups as $group)
            <a href="{{ route('teacher.groups.show', $group) }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-semibold">{{ $group->course->title }} — {{ $group->group_name }}</h4>
                        <p class="text-sm text-gray-500">{{ is_array($group->class_days) ? implode(', ', $group->class_days) : $group->class_days }} | {{ $group->class_time }}</p>
                        <p class="text-sm text-gray-500">{{ $group->activeStudents->count() }} active students</p>
                    </div>
                    @if ($group->todaysLink())
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">✅ Link posted today</span>
                    @else
                    <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">⏳ Link not posted</span>
                    @endif
                </div>
            </a>
            @empty
            <p class="text-gray-500">No groups assigned to you yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>