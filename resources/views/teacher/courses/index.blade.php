<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('db.Teacher Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-5 text-center border-l-4 border-teal-500">
                    <div class="text-3xl font-bold">{{ $groups->count() }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ __('db.My Groups') }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5 text-center border-l-4 border-blue-500">
                    <div class="text-3xl font-bold">{{ $groups->sum(fn($g) => $g->activeStudents->count()) }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ __('db.Active Students') }}</div>
                </div>
                <a href="{{ route('teacher.students.index') }}" class="bg-white rounded-lg shadow-sm p-5 text-center border-l-4 border-green-500 hover:shadow-md">
                    <div class="text-3xl font-bold">{{ $groups->sum(fn($g) => $g->activeStudents->count()) }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ __('db.All Students') }}</div>
                </a>
                <a href="{{ route('teacher.schedule') }}" class="bg-white rounded-lg shadow-sm p-5 text-center border-l-4 border-purple-500 hover:shadow-md">
                    <div class="text-2xl font-bold">📅</div>
                    <div class="text-sm text-gray-500 mt-1">{{ __('db.My Schedule') }}</div>
                </a>
            </div>

            {{-- Today's Classes --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __("db.Today's Classes (:date)", ['date' => now()->format('l, d M')]) }}</h3>
                @php $today = strtolower(now()->format('D')); $today = ucfirst(substr($today, 0, 3)); @endphp
                @forelse ($groups->filter(fn($g) => in_array($today, $g->class_days ?? [])) as $group)
                <div class="flex justify-between items-center border rounded-lg p-4 mb-2">
                    <div>
                        <p class="font-medium">{{ $group->course->title }} — {{ $group->group_name }}</p>
                        <p class="text-sm text-gray-500">{{ $group->class_time }} | {{ $group->activeStudents->count() }} {{ __('db.students') }}</p>
                    </div>
                    @if ($group->todaysLink())
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">✅ {{ __('db.Link posted') }}</span>
                    @else
                    <a href="{{ route('teacher.groups.show', $group) }}"
                        class="text-xs bg-yellow-500 text-white px-3 py-1.5 rounded hover:bg-yellow-600">
                        {{ __('db.Post Link') }}
                    </a>
                    @endif
                </div>
                @empty
                <p class="text-gray-400 text-sm">{{ __('db.No classes scheduled for today.') }}</p>
                @endforelse
            </div>

            {{-- All Groups --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.All My Groups') }}</h3>
                <div class="space-y-3">
                    @forelse ($groups as $group)
                    <a href="{{ route('teacher.groups.show', $group) }}"
                        class="flex justify-between items-center border rounded-lg p-4 hover:shadow-md transition">
                        <div>
                            <p class="font-medium">{{ $group->course->title }} — {{ $group->group_name }}</p>
                            <p class="text-sm text-gray-500">
                                {{ is_array($group->class_days) ? implode(', ', $group->class_days) : $group->class_days }}
                                | {{ $group->class_time }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $group->activeStudents->count() }} {{ __('db.active students') }}</p>
                        </div>
                        <div class="text-right">
                            @if ($group->todaysLink())
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full block mb-1">✅ {{ __('db.Link posted') }}</span>
                            @else
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full block mb-1">{{ __('db.No link yet') }}</span>
                            @endif
                            <span class="text-xs text-teal-600">{{ __('db.Open') }} →</span>
                        </div>
                    </a>
                    @empty
                    <p class="text-gray-400 text-sm">{{ __('db.No groups assigned yet.') }}</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>