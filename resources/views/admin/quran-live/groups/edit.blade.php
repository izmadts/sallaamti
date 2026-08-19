<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Update Group — {{ $course->title }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('admin.quran-class-groups.update',  $group ) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label value="Group Name" />
                    <x-text-input name="group_name" class="w-full mt-1" value="{{ $group->group_name }}" placeholder="e.g. Group A - Morning (Males)" required />
                </div>
                <div>
                    <x-input-label value="Assign Teacher" />
                    <select name="teacher_id" class="border-gray-300 rounded-md w-full mt-1">
                        <option value="">-- Assign later --</option>
                        @foreach ($teachers as $t)
                        <option value="{{ $t->id }}" {{ $t->id == $group->teacher_id ? 'selected' : '' }}>
                            {{ $t->name }} — {{ $t->isApprovedTeacher() ? '✅ Approved' : '⏳ Pending vetting' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Class Days" />
                    <div class="grid grid-cols-4 gap-2 mt-1 text-sm">
                        @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                        <label class="flex items-center gap-1">
                            <input type="checkbox" name="class_days[]" value="{{ $day }}" {{ in_array($day, $group->class_days) ? 'checked' : '' }}> {{ $day }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Class Time" />
                        <x-text-input name="class_time" class="w-full mt-1" value="{{ $course->class_time }}" placeholder="6:00 PM - 6:45 PM" required />
                    </div>
                    <div>
                        <x-input-label value="Timezone" />
                        <x-text-input name="timezone" class="w-full mt-1" value="Asia/Karachi" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Gender" />
                        <select name="gender" class="border-gray-300 rounded-md w-full mt-1" required>
                            <option value="mixed">Mixed</option>
                            <option value="male">Male Only</option>
                            <option value="female">Female Only</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Max Students" />
                        <x-text-input name="max_students" type="number" class="w-full mt-1" value="{{ $course->max_students_per_group }}" required />
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active" checked>
                    <label for="is_active" class="text-sm">Active (students can be assigned)</label>
                </div>
                <x-primary-button>Update Group</x-primary-button>
            </form>
        </div>
    </div>
</x-admin-layout>