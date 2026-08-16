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

                <form method="POST" action="{{ route('nikah.create.step.save', 'basic') }}" class="space-y-6">
                    @csrf

                    <x-nikah-section :title="__('db.Basic Information')" icon="🧍" color="blue">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="age" :value="__('db.Age')" />
                                <x-text-input id="age" name="age" type="number" class="w-full mt-1" :value="old('age', $data['age'] ?? '')" required
                                    placeholder="{{ __('db.e.g. 27') }}" title="{{ __('db.Your current age in years — must be 18 or older.') }}" />
                            </div>
                            <div>
                                <x-input-label for="gender" :value="__('db.Gender')" />
                                @php $genderVal = old('gender', $accountGender ?? ''); @endphp
                                <select id="gender" name="gender" required class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Matching is opposite-gender, so this decides whose profiles you\'ll see.') }}">
                                    <option value="">{{ __('db.Select') }}</option>
                                    <option value="male" {{ $genderVal === 'male' ? 'selected' : '' }}>{{ __('db.Male') }}</option>
                                    <option value="female" {{ $genderVal === 'female' ? 'selected' : '' }}>{{ __('db.Female') }}</option>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">{{ __('db.This also updates your account profile — used for opposite-gender matching.') }}</p>
                            </div>
                            @php
                                $heightOptions = [];
                                for ($ft = 4; $ft <= 7; $ft++) {
                                    for ($in = 0; $in <= 11; $in++) {
                                        if ($ft === 7 && $in > 0) break;
                                        $heightOptions[] = $ft . "'" . $in . '"';
                                    }
                                }
                                // $data['height'] holds the already-resolved value from a prior
                                // save (which is the custom text itself when "Other" was chosen,
                                // not the literal sentinel) — so infer "Other" from a value that
                                // doesn't match any fixed option, same as the edit form does.
                                $heightVal = old('height', $data['height'] ?? '');
                                $heightOtherRaw = old('height_other', $data['height_other'] ?? '');
                                $heightIsOther = $heightVal === 'Other' || ($heightVal !== '' && !in_array($heightVal, $heightOptions));
                                $heightOtherPrefill = $heightOtherRaw !== '' ? $heightOtherRaw : ($heightIsOther ? $heightVal : '');
                            @endphp
                            <div x-data="{ ht: '{{ $heightIsOther ? 'Other' : $heightVal }}' }">
                                <x-input-label for="height" :value="__('db.Height')" />
                                <select id="height" name="height" x-model="ht" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Optional — a fixed list keeps this consistent for matching.') }}">
                                    <option value="">{{ __('db.Prefer not to say') }}</option>
                                    @foreach ($heightOptions as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                    @endforeach
                                    <option value="Other">{{ __('db.Other') }}</option>
                                </select>
                                <div x-show="ht === 'Other'" x-cloak class="mt-2">
                                    <x-text-input name="height_other" type="text" class="w-full" placeholder="{{ __('db.e.g. 173cm') }}" value="{{ $heightOtherPrefill }}" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="marital_status" :value="__('db.Marital Status')" />
                                @php $ms = old('marital_status', $data['marital_status'] ?? 'never_married'); @endphp
                                <select id="marital_status" name="marital_status" required class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Please be honest — this matters for compatibility and is checked against your CNIC where relevant.') }}">
                                    <option value="never_married" {{ $ms === 'never_married' ? 'selected' : '' }}>{{ __('db.Never Married') }}</option>
                                    <option value="divorced" {{ $ms === 'divorced' ? 'selected' : '' }}>{{ __('db.Divorced') }}</option>
                                    <option value="widowed" {{ $ms === 'widowed' ? 'selected' : '' }}>{{ __('db.Widowed') }}</option>
                                    <option value="separated" {{ $ms === 'separated' ? 'selected' : '' }}>{{ __('db.Separated') }}</option>
                                    <option value="married" {{ $ms === 'married' ? 'selected' : '' }}>{{ __('db.Married') }}</option>
                                </select>
                            </div>
                            @php $educationLevels = ['Matric / O-Levels', 'Intermediate / A-Levels', "Bachelor's", "Master's", 'MPhil / MS', 'PhD', 'Madrassah / Islamic Education']; @endphp
                            <div>
                                <x-input-label for="education" :value="__('db.Education')" />
                                <x-text-input id="education" name="education" type="text" list="education-list" autocomplete="off" class="w-full mt-1" :value="old('education', $data['education'] ?? '')"
                                    placeholder="{{ __('db.Type to search, or pick from the list') }}" title="{{ __('db.Start typing to filter, or click the list to pick directly — you can also type something not listed.') }}" />
                                {{-- A <datalist> option's value is both what's suggested AND what gets
                                     submitted if picked, so it can't be translated the way a <select>
                                     option's separate label/value pair can — kept in canonical English
                                     so it stays consistent with what's actually stored and searched. --}}
                                <datalist id="education-list">
                                    @foreach ($educationLevels as $lvl)
                                    <option value="{{ $lvl }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <x-input-label for="profession" :value="__('db.Profession')" />
                                <x-text-input id="profession" name="profession" type="text" class="w-full mt-1" :value="old('profession', $data['profession'] ?? '')"
                                    placeholder="{{ __('db.e.g. Software Engineer, Teacher, Doctor') }}" title="{{ __('db.What you currently do for work.') }}" />
                            </div>
                            <div>
                                <x-input-label for="city" :value="__('db.City')" />
                                <x-text-input id="city" name="city" type="text" class="w-full mt-1" :value="old('city', $data['city'] ?? '')" required
                                    placeholder="{{ __('db.e.g. Karachi, Lahore, Islamabad') }}" title="{{ __('db.The city you currently live in.') }}" />
                            </div>
                            <div>
                                <x-input-label for="country" :value="__('db.Country')" />
                                <x-text-input id="country" name="country" type="text" class="w-full mt-1" :value="old('country', $data['country'] ?? 'Pakistan')"
                                    placeholder="{{ __('db.e.g. Pakistan') }}" title="{{ __('db.The country you currently live in.') }}" />
                            </div>
                        </div>
                    </x-nikah-section>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('db.Next: Family & Background') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
