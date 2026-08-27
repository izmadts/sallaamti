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
                        <x-text-input name="level_number" class="w-full mt-1" placeholder="Level 1" />
                    </div>
                    <div>
                        <x-input-label value="Category" />
                        <select name="category" class="border-gray-300 rounded-md w-full mt-1">
                            <option value="">-- Select --</option>
                            <option value="Nazrah">Nazrah Quran</option>
                            <option value="Tajweed">Tajweed</option>
                            <option value="Translation">Translation & Tafseer</option>
                            <option value="Arabic Grammar">Arabic Grammar</option>
                            <option value="Advance">Advance (Optional)</option>
                            <option value="Special">Special Program</option>
                        </select>
                    </div>
                </div>

                <div>
                    <x-input-label value="Title" />
                    <x-text-input name="title" class="w-full mt-1" required placeholder="e.g. Nazrah Quran — Level 1" />
                </div>

                <div>
                    <x-input-label value="Duration" />
                    <x-text-input name="duration" class="w-full mt-1" placeholder="e.g. 6-12 Months" />
                </div>

                <div>
                    <x-input-label value="Description / Outcome" />
                    <input id="trix-outcome" type="hidden" name="outcome" value="{{ old('outcome') }}">
                    <trix-editor input="trix-outcome" placeholder="Student will be able to..."></trix-editor>
                </div>

                <div>
                    <x-input-label value="Syllabus Topics (one per line)" />
                    <textarea name="topics_raw" rows="6" class="border-gray-300 rounded-md w-full mt-1"
                        placeholder="Arabic Alphabet (Makharij)&#10;Zabar, Zair, Pesh&#10;Basic Tajweed Rules"></textarea>
                    <p class="text-xs text-gray-400 mt-1">Each line becomes a topic bullet point on the course page.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Monthly Fee (Rs. / $)" />
                        <x-text-input name="monthly_fee" type="number" step="0.01" class="w-full mt-1" required />
                    </div>
                    <div>
                        <x-input-label value="Max Students Per Group" />
                        <x-text-input name="max_students_per_group" type="number" class="w-full mt-1" value="10" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Recommended min age (optional)" />
                        <x-text-input type="number" name="min_age" min="1" max="120" class="w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Recommended max age (optional)" />
                        <x-text-input type="number" name="max_age" min="1" max="120" class="w-full mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label value="Gender Preference" />
                    <select name="gender_preference" class="border-gray-300 rounded-md w-full mt-1">
                        <option value="both">Both (Male & Female)</option>
                        <option value="male">Male Only</option>
                        <option value="female">Female Only</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" value="1" id="is_published">
                    <label for="is_published" class="text-sm">Publish (visible to students)</label>
                </div>

                <x-primary-button>Create Course Level</x-primary-button>
            </form>
        </div>
    </div>
</x-admin-layout>