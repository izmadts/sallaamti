<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Quran Admissions</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">


            @forelse ($admissions as $admission)
            <div class="bg-white shadow-sm rounded-lg p-5">
                <div class="flex justify-between items-start flex-wrap gap-3">
                    <div>
                        <h3 class="font-semibold">{{ $admission->student_name }}
                            <span class="text-sm font-normal text-gray-500">({{ $admission->user->name }})</span>
                        </h3>
                        <p class="text-sm text-gray-500">
                            Age: {{ $admission->student_age }} |
                            Gender: {{ ucfirst($admission->student_gender) }} |
                            Teacher Pref: {{ ucfirst(str_replace('_', ' ', $admission->teacher_preference)) }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Level Requested: <strong>{{ $admission->selected_level ?: 'Not specified' }}</strong> |
                            Class Type: {{ ucfirst(str_replace('_', ' ', $admission->class_type)) }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Preferred Days: {{ is_array($admission->preferred_days) ? implode(', ', $admission->preferred_days) : $admission->preferred_days }} |
                            Time: {{ $admission->preferred_time }} |
                            Timezone: {{ $admission->timezone }}
                        </p>
                        <p class="text-sm text-gray-500">WhatsApp: {{ $admission->whatsapp_number }} | Country: {{ $admission->country }}</p>
                        @if ($admission->comments)
                        <p class="text-sm text-gray-600 mt-1 italic">"{{ $admission->comments }}"</p>
                        @endif
                        @if ($admission->assigned_group_id)
                        <p class="text-sm text-green-600 mt-1">✅ Assigned to: {{ $admission->assignedGroup?->group_name }}</p>
                        @endif
                    </div>

                    <span class="text-xs px-2 py-1 rounded-full
                            {{ $admission->status === 'assigned' ? 'bg-green-100 text-green-800' :
                               ($admission->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($admission->status) }}
                    </span>
                </div>

                @if ($admission->status === 'pending')
                <div class="mt-4 flex gap-3 flex-wrap">
                    <form method="POST" action="{{ route('admin.quran-admissions.assign', $admission) }}" class="flex gap-2">
                        @csrf
                        <select name="group_id" class="border-gray-300 rounded text-sm" required>
                            <option value="">-- Select Group --</option>
                            @foreach (\App\Models\QuranClassGroup::with('course')->where('is_active', true)->get() as $group)
                            <option value="{{ $group->id }}">{{ $group->course->title }} — {{ $group->group_name }} ({{ $group->class_time }})</option>
                            @endforeach
                        </select>
                        <button class="bg-green-600 text-white text-sm px-4 py-2 rounded">Assign to Group</button>
                    </form>

                    <form method="POST" action="{{ route('admin.quran-admissions.reject', $admission) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="admin_notes" placeholder="Reason for rejection" class="border-gray-300 rounded text-sm px-2" required>
                        <button class="bg-red-600 text-white text-sm px-4 py-2 rounded">Reject</button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <p class="text-gray-500">No admissions yet.</p>
            @endforelse

            {{ $admissions->links() }}
        </div>
    </div>
</x-admin-layout>