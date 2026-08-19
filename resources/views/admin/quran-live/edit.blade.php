<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Edit Quran Live Course</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('admin.quran-live-courses.update', $course) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Level Number" />
                        <x-text-input name="level_number" class="w-full mt-1" :value="old('level_number', $course->level_number)" placeholder="Level 1" />
                    </div>
                    <div>
                        <x-input-label value="Category" />
                        <select name="category" class="border-gray-300 rounded-md w-full mt-1">
                            @foreach (['Nazrah' => 'Nazrah Quran', 'Tajweed' => 'Tajweed', 'Translation' => 'Translation & Tafseer', 'Arabic Grammar' => 'Arabic Grammar', 'Advance' => 'Advance (Optional)', 'Special' => 'Special Program'] as $val => $label)
                            <option value="{{ $val }}" {{ old('category', $course->category) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <x-input-label value="Title" />
                    <x-text-input name="title" class="w-full mt-1" :value="old('title', $course->title)" required />
                </div>

                <div>
                    <x-input-label value="Duration" />
                    <x-text-input name="duration" class="w-full mt-1" :value="old('duration', $course->duration)" placeholder="e.g. 6-12 Months" />
                </div>

                <div>
                    <x-input-label value="Description / Outcome" />
                    <textarea name="outcome" rows="2" class="border-gray-300 rounded-md w-full mt-1">{{ old('outcome', $course->outcome) }}</textarea>
                </div>

                <div>
                    <x-input-label value="Syllabus Topics (one per line)" />
                    <textarea name="topics_raw" rows="6" class="border-gray-300 rounded-md w-full mt-1">{{ old('topics_raw', is_array($course->topics) ? implode("\n", $course->topics) : $course->topics) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Each line becomes a topic bullet point.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Recommended min age (optional)" />
                        <x-text-input type="number" name="min_age" min="1" max="120" class="w-full mt-1" :value="old('min_age', $course->min_age)" />
                    </div>
                    <div>
                        <x-input-label value="Recommended max age (optional)" />
                        <x-text-input type="number" name="max_age" min="1" max="120" class="w-full mt-1" :value="old('max_age', $course->max_age)" />
                    </div>
                </div>

                <div>
                    <x-input-label value="Assign Teacher" />
                    <select name="teacher_id" class="border-gray-300 rounded-md w-full mt-1">
                        <option value="">-- None yet --</option>
                        @foreach ($teachers as $t)
                        <option value="{{ $t->id }}" {{ old('teacher_id', $course->teacher_id) == $t->id ? 'selected' : '' }}>{{ $t->name }} — {{ $t->isApprovedTeacher() ? '✅ Approved' : '⏳ Pending vetting' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label value="Class Days" />
                    <div class="grid grid-cols-4 gap-2 mt-1 text-sm">
                        @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                        <label class="flex items-center gap-1">
                            <input type="checkbox" name="class_days[]" value="{{ $day }}"
                                {{ in_array($day, old('class_days', $course->class_days ?? [])) ? 'checked' : '' }}>
                            {{ $day }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Class Time" />
                        <x-text-input name="class_time" class="w-full mt-1" :value="old('class_time', $course->class_time)" placeholder="6:00 PM - 7:00 PM" />
                    </div>
                    <div>
                        <x-input-label value="Monthly Fee (Rs.)" />
                        <x-text-input name="monthly_fee" type="number" step="0.01" class="w-full mt-1" :value="old('monthly_fee', $course->monthly_fee)" required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Max Students Per Group" />
                        <x-text-input name="max_students_per_group" type="number" class="w-full mt-1" :value="old('max_students_per_group', $course->max_students_per_group)" />
                    </div>
                    <div>
                        <x-input-label value="Gender Preference" />
                        <select name="gender_preference" class="border-gray-300 rounded-md w-full mt-1">
                            @foreach (['both' => 'Both', 'male' => 'Male Only', 'female' => 'Female Only'] as $val => $label)
                            <option value="{{ $val }}" {{ old('gender_preference', $course->gender_preference) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" value="1" id="is_published"
                        {{ old('is_published', $course->is_published) ? 'checked' : '' }}>
                    <label for="is_published" class="text-sm">Publish (visible to students)</label>
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('admin.quran-live-courses.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
                    <x-primary-button>Update Course</x-primary-button>
                </div>

            </form>
        </div>
    </div>
</x-admin-layout>