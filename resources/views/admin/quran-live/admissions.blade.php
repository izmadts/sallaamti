<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Quran Admissions</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))<div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>@endif
            @if (session('error'))<div class="p-4 bg-red-50 text-red-700 rounded">{{ session('error') }}</div>@endif

            @forelse ($admissions as $admission)
            <div class="bg-white shadow-sm rounded-lg p-5">
                <div class="flex justify-between items-start flex-wrap gap-3">
                    <div>
                        <h3 class="font-semibold">{{ $admission->student_name }}
                            <span class="text-sm font-normal text-gray-500">({{ $admission->user?->name ?? 'deleted account' }})</span>
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
                            {{ match($admission->status) {
                                'assigned', 'completed' => 'bg-green-100 text-green-800',
                                'rejected', 'dropped' => 'bg-red-100 text-red-800',
                                default => 'bg-yellow-100 text-yellow-800',
                            } }}">
                        {{ ucfirst($admission->status) }}
                    </span>
                </div>

                @if ($admission->status === 'pending')
                <div class="mt-4 flex gap-3 flex-wrap">
                    <form method="POST" action="{{ route('admin.quran-admissions.assign', $admission) }}" class="flex gap-2">
                        @csrf
                        <select name="group_id" class="border-gray-300 rounded text-sm" required>
                            <option value="">-- Select Group --</option>
                            @php
                                // Scoped to THIS admission's own course — it
                                // used to list every active group system-wide,
                                // which let a Nazrah Level 1 admission be
                                // assigned straight into an unrelated
                                // Tajweed/Advanced group with two clicks.
                                $eligibleGroups = $admission->course->classGroups()->where('is_active', true)->get();
                            @endphp
                            @foreach ($eligibleGroups as $group)
                            @php $genderMismatch = $group->gender !== 'mixed' && $group->gender !== $admission->student_gender; @endphp
                            <option value="{{ $group->id }}" {{ ($genderMismatch || $group->isFull()) ? 'disabled' : '' }}>
                                {{ $group->group_name }} ({{ $group->class_time }}, {{ ucfirst($group->gender) }}{{ $group->teacher ? ' — ' . $group->teacher->name : '' }}){{ $group->isFull() ? ' — FULL' : ($genderMismatch ? ' — gender mismatch' : '') }}
                            </option>
                            @endforeach
                        </select>
                        @if ($eligibleGroups->isEmpty())
                        <p class="text-xs text-red-500 mt-1">No active groups exist yet for {{ $admission->course->title }} — create one first.</p>
                        @endif
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