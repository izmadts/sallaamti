<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ $course->title }} — Class Groups</h2>
            <a href="{{ route('admin.quran-live-courses.groups.create', $course) }}" class="bg-gray-800 text-white text-sm px-4 py-2 rounded">+ New Group</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            @forelse ($groups as $group)
            <div class="bg-white shadow-sm rounded-lg p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold">{{ $group->group_name }}</h3>
                        <p class="text-sm text-gray-500">Teacher: {{ $group->teacher?->name ?? 'Unassigned' }}</p>
                        <p class="text-sm text-gray-500">{{ is_array($group->class_days) ? implode(', ', $group->class_days) : $group->class_days }} | {{ $group->class_time }}</p>
                        <p class="text-sm text-gray-500">{{ $group->students->count() }}/{{ $group->max_students }} students | {{ ucfirst($group->gender) }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.quran-class-groups.edit', $group) }}" class="text-sm text-blue-600">Edit</a>
                        <span class="text-xs px-2 py-1 rounded-full {{ $group->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $group->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                @if ($group->students->count() > 0)
                <div class="mt-3 border-t pt-3">
                    <p class="text-xs text-gray-500 mb-2">Students:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($group->students as $gs)
                        <div class="flex items-center gap-2 bg-gray-50 rounded px-2 py-1 text-xs">
                            <span>{{ $gs->user->name }}</span>
                            <form method="POST" action="{{ route('admin.quran-group-students.status', $gs) }}">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="text-xs border-0 bg-transparent">
                                    @foreach (['active', 'on_hold', 'completed', 'dropped'] as $s)
                                    <option value="{{ $s }}" {{ $gs->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @empty
            <p class="text-gray-500">No groups yet. Create your first group above.</p>
            @endforelse

        </div>
    </div>
</x-admin-layout>