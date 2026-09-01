<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">New Quran Live Course / Level</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('admin.quran-live-courses.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Level Number (e.g. Level 1)" />
                        <x-text-input name="level_number" class="w-full mt-1" :value="old('level_number')" placeholder="Level 1" />
                    </div>
                    <div>
                        <x-input-label value="Category" />
                        <select name="category" class="border-gray-300 rounded-md w-full mt-1">
                            <option value="">-- Select --</option>
                            @foreach (['Nazrah' => 'Nazrah Quran', 'Tajweed' => 'Tajweed', 'Translation' => 'Translation & Tafseer', 'Arabic Grammar' => 'Arabic Grammar', 'Advance' => 'Advance (Optional)', 'Special' => 'Special Program'] as $val => $label)
                            <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <x-input-label value="Title" />
                    <x-text-input name="title" class="w-full mt-1" :value="old('title')" required placeholder="e.g. Nazrah Quran — Level 1" />
                </div>

                <div>
                    <x-input-label value="Duration" />
                    <x-text-input name="duration" class="w-full mt-1" :value="old('duration')" placeholder="e.g. 6-12 Months" />
                </div>

                <div>
                    <x-input-label value="Description / Outcome" />
                    <input id="trix-outcome" type="hidden" name="outcome" value="{{ old('outcome') }}">
                    <trix-editor input="trix-outcome" placeholder="Student will be able to..."></trix-editor>
                </div>

                <div>
                    <x-input-label value="Syllabus Topics (one per line)" />
                    <textarea name="topics_raw" rows="6" class="border-gray-300 rounded-md w-full mt-1"
                        placeholder="Arabic Alphabet (Makharij)&#10;Zabar, Zair, Pesh&#10;Basic Tajweed Rules">{{ old('topics_raw') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Each line becomes a topic bullet point on the course page.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Recommended min age (optional)" />
                        <x-text-input type="number" name="min_age" min="1" max="120" class="w-full mt-1" :value="old('min_age')" />
                    </div>
                    <div>
                        <x-input-label value="Recommended max age (optional)" />
                        <x-text-input type="number" name="max_age" min="1" max="120" class="w-full mt-1" :value="old('max_age')" />
                    </div>
                </div>

                <div>
                    <x-input-label value="Assign Teacher (optional — can also be set per group later)" />
                    <select name="teacher_id" class="border-gray-300 rounded-md w-full mt-1">
                        <option value="">-- None yet --</option>
                        @foreach ($teachers as $t)
                        <option value="{{ $t->id }}" {{ (int) old('teacher_id') === $t->id ? 'selected' : '' }}>{{ $t->name }} — {{ $t->isApprovedTeacher() ? '✅ Approved' : '⏳ Pending vetting' }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">This is the course's default teacher shown in the catalog before admission — each class group can have its own teacher too.</p>
                </div>

                <div>
                    <x-input-label value="Typical Class Days (optional — shown in the catalog; each group sets its own real schedule)" />
                    <div class="grid grid-cols-4 gap-2 mt-1 text-sm">
                        @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                        <label class="flex items-center gap-1">
                            <input type="checkbox" name="class_days[]" value="{{ $day }}" {{ in_array($day, old('class_days', [])) ? 'checked' : '' }}>
                            {{ $day }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Typical Class Time" />
                        <x-text-input name="class_time" class="w-full mt-1" :value="old('class_time')" placeholder="6:00 PM - 7:00 PM" />
                    </div>
                    <div>
                        <x-input-label value="Monthly Fee (Rs. / $)" />
                        <x-text-input name="monthly_fee" type="number" step="0.01" class="w-full mt-1" :value="old('monthly_fee')" required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Max Students Per Group" />
                        <x-text-input name="max_students_per_group" type="number" class="w-full mt-1" :value="old('max_students_per_group', 10)" />
                    </div>
                    <div>
                        <x-input-label value="Gender Preference" />
                        <select name="gender_preference" class="border-gray-300 rounded-md w-full mt-1">
                            @foreach (['both' => 'Both (Male & Female)', 'male' => 'Male Only', 'female' => 'Female Only'] as $val => $label)
                            <option value="{{ $val }}" {{ old('gender_preference', 'both') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published') ? 'checked' : '' }}>
                    <label for="is_published" class="text-sm">Publish (visible to students)</label>
                </div>

                <x-primary-button>Create Course Level</x-primary-button>
            </form>
        </div>
    </div>
</x-admin-layout>
