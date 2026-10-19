<x-dynamic-component :component="$routePrefix === 'matchmaker' ? 'matchmaker-layout' : 'admin-layout'">
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route($routePrefix === 'matchmaker' ? 'matchmaker.nikah.index' : 'admin.nikah.verifications') }}" class="text-gray-400 hover:text-gray-600">Nikah Profiles</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Create Profile</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" :current="$step" />

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <p class="font-medium text-sm mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route($routePrefix . '.nikah.profiles.create.step.save', 'family') }}" class="space-y-6">
                    @csrf

                    <x-nikah-section title="Family & Guardian Information (Sarparast Family)" icon="👨‍👩‍👧" color="amber">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="caste" value="Caste (optional)" />
                                <x-text-input id="caste" name="caste" type="text" class="w-full mt-1" :value="old('caste', $data['caste'] ?? '')" placeholder="e.g. Rajput, Sheikh, Syed" />
                            </div>
                            <div>
                                <x-input-label for="ethnicity" value="Ethnicity (optional)" />
                                <x-text-input id="ethnicity" name="ethnicity" type="text" class="w-full mt-1" :value="old('ethnicity', $data['ethnicity'] ?? '')" placeholder="e.g. Punjabi, Pashtun, Sindhi, Muhajir" />
                            </div>
                            @php $familyTypeOptions = ['Joint Family', 'Nuclear Family', 'Living with In-Laws']; @endphp
                            <div>
                                <x-input-label for="family_type" value="Family Type (optional)" />
                                <x-searchable-select name="family_type" :options="$familyTypeOptions" :value="old('family_type', $data['family_type'] ?? '')" placeholder="Type to search or pick" />
                            </div>
                            <div class="sm:col-span-2" x-data="{ other: {{ old('language_other', $data['language_other'] ?? '') ? 'true' : 'false' }} }">
                                <x-input-label value="Language(s) Spoken (optional)" />
                                @php $existingLanguages = old('languages', $data['languages'] ?? []); @endphp
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach (['Urdu', 'English', 'Punjabi', 'Pashto', 'Sindhi', 'Saraiki', 'Balochi'] as $lang)
                                    <label class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-full border cursor-pointer transition hover:bg-gray-50"
                                        style="border-color: #d1d5db">
                                        <input type="checkbox" name="languages[]" value="{{ $lang }}" {{ in_array($lang, $existingLanguages) ? 'checked' : '' }} class="rounded">
                                        {{ $lang }}
                                    </label>
                                    @endforeach
                                    <label class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-full border cursor-pointer transition hover:bg-gray-50" style="border-color: #d1d5db">
                                        <input type="checkbox" x-model="other" class="rounded">
                                        Other
                                    </label>
                                </div>
                                <div x-show="other" x-cloak class="mt-2">
                                    <x-text-input name="language_other" type="text" class="w-full" placeholder="e.g. Balti, Hindko, Arabic" :value="old('language_other', $data['language_other'] ?? '')" />
                                </div>
                            </div>
                            @php $guardianRelOptions = ['Self', 'Father', 'Mother', 'Brother', 'Sister', 'Uncle', 'Aunt', 'Grandfather', 'Grandmother']; @endphp
                            <div>
                                <x-input-label for="guardian_relation" value="Guardian Relation (Sarparast Family) (optional)" />
                                <x-searchable-select name="guardian_relation" :options="$guardianRelOptions" :value="old('guardian_relation', $data['guardian_relation'] ?? '')" placeholder="Type to search or pick" />
                            </div>
                            <div>
                                <x-input-label for="guardian_name" value="Guardian Name (Sarparast Family)" />
                                <x-text-input id="guardian_name" name="guardian_name" type="text" class="w-full mt-1" :value="old('guardian_name', $data['guardian_name'] ?? '')" required
                                    placeholder="e.g. Muhammad Ahmed" />
                            </div>
                            <div>
                                <x-input-label for="guardian_contact" value="Guardian Contact (Sarparast Family) (optional)" />
                                <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="w-full mt-1" :value="old('guardian_contact', $data['guardian_contact'] ?? '')"
                                    placeholder="e.g. 03001234567" title="Fill this in now if you have it, or leave blank and add it later — the client can also add it themselves once logged in." />
                            </div>
                        </div>
                    </x-nikah-section>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route($routePrefix . '.nikah.profiles.create.step', 'basic') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← Back</a>
                        <x-primary-button>Next: Deen & Lifestyle →</x-primary-button>
                    </div>
                </form>

                @if (auth()->user()->hasRole('admin'))
                <form method="POST" action="{{ route($routePrefix . '.nikah.profiles.create.step.skip', 'family') }}" class="text-right mt-2">
                    @csrf
                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 underline">Skip (admin preview — no data saved)</button>
                </form>
                @endif

            </div>
        </div>
    </div>
</x-dynamic-component>
