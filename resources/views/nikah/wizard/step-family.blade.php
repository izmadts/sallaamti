<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.Create Your Nikah Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" :current="$step" />

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('nikah.create.step.save', 'family') }}" class="space-y-6">
                    @csrf

                    <x-nikah-section :title="__('db.Family & Background')" icon="👨‍👩‍👧" color="amber">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="caste" :value="__('db.Caste (optional)')" />
                                <x-text-input id="caste" name="caste" type="text" class="w-full mt-1" :value="old('caste', $data['caste'] ?? '')"
                                    placeholder="{{ __('db.e.g. Rajput, Sheikh, Syed') }}" title="{{ __('db.Only if caste/biradari matters to your family — leave blank if not.') }}" />
                            </div>
                            @php $familyTypeOptions = ['Joint Family', 'Nuclear Family', 'Living with In-Laws']; @endphp
                            <div>
                                <x-input-label for="family_type" :value="__('db.Family Type')" />
                                <x-text-input id="family_type" name="family_type" type="text" list="family-type-list" autocomplete="off" class="w-full mt-1" :value="old('family_type', $data['family_type'] ?? '')"
                                    placeholder="{{ __('db.Type to search, or pick from the list') }}" title="{{ __('db.Do you live in a joint family or on your own as a separate household?') }}" />
                                <datalist id="family-type-list">
                                    @foreach ($familyTypeOptions as $ftOpt)
                                    <option value="{{ $ftOpt }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <x-input-label for="ethnicity" :value="__('db.Ethnicity (optional)')" />
                                <x-text-input id="ethnicity" name="ethnicity" type="text" class="w-full mt-1" :value="old('ethnicity', $data['ethnicity'] ?? '')"
                                    placeholder="{{ __('db.e.g. Punjabi, Pashtun, Sindhi, Muhajir') }}" title="{{ __('db.Your ethnic background, if you\'d like to share it.') }}" />
                            </div>
                            <div class="sm:col-span-2" x-data="{ other: {{ old('language_other', $data['language_other'] ?? '') ? 'true' : 'false' }} }">
                                <x-input-label :value="__('db.Language(s) Spoken (optional)')" />
                                @php $existingLanguages = old('languages', $data['languages'] ?? []); @endphp
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach (['Urdu', 'English', 'Punjabi', 'Pashto', 'Sindhi', 'Saraiki', 'Balochi'] as $lang)
                                    <label class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-full border cursor-pointer transition hover:bg-gray-50"
                                        style="border-color: #d1d5db">
                                        <input type="checkbox" name="languages[]" value="{{ $lang }}" {{ in_array($lang, $existingLanguages) ? 'checked' : '' }} class="rounded">
                                        {{ __('db.' . $lang) }}
                                    </label>
                                    @endforeach
                                    <label class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-full border cursor-pointer transition hover:bg-gray-50" style="border-color: #d1d5db">
                                        <input type="checkbox" x-model="other" class="rounded">
                                        {{ __('db.Other') }}
                                    </label>
                                </div>
                                <div x-show="other" x-cloak class="mt-2">
                                    <x-text-input name="language_other" type="text" class="w-full" placeholder="{{ __('db.e.g. Balti, Hindko, Arabic') }}" :value="old('language_other', $data['language_other'] ?? '')" />
                                </div>
                            </div>
                            @php $guardianRelOptions = ['Father', 'Mother', 'Brother', 'Sister', 'Uncle', 'Aunt', 'Grandfather', 'Grandmother']; @endphp
                            <div>
                                <x-input-label for="guardian_relation" :value="__('db.Guardian Relation')" />
                                <x-text-input id="guardian_relation" name="guardian_relation" type="text" list="guardian-relation-list" autocomplete="off" class="w-full mt-1" :value="old('guardian_relation', $data['guardian_relation'] ?? '')"
                                    placeholder="{{ __('db.Type to search, or pick from the list') }}" title="{{ __('db.Your relationship to the guardian named below.') }}" />
                                <datalist id="guardian-relation-list">
                                    @foreach ($guardianRelOptions as $relOpt)
                                    <option value="{{ $relOpt }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <x-input-label for="guardian_name" :value="__('db.Guardian Name')" />
                                <x-text-input id="guardian_name" name="guardian_name" type="text" class="w-full mt-1" :value="old('guardian_name', $data['guardian_name'] ?? '')" required
                                    placeholder="{{ __('db.e.g. Muhammad Ahmed') }}" title="{{ __('db.Your Wali/guardian\'s full name — so we can involve your family in the process.') }}" />
                            </div>
                            <div>
                                <x-input-label for="guardian_contact" :value="__('db.Guardian Contact Number')" />
                                <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="w-full mt-1" :value="old('guardian_contact', $data['guardian_contact'] ?? '')" required
                                    placeholder="{{ __('db.e.g. 03001234567') }}" title="{{ __('db.A number where we or an interested family can reach your guardian.') }}" />
                            </div>
                        </div>
                    </x-nikah-section>

                    <div class="flex justify-between">
                        <a href="{{ route('nikah.create.step', 'basic') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button>{{ __('db.Next: Deen & Lifestyle') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
