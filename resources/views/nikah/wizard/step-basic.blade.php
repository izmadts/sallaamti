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

                    @php
                        $ms = old('marital_status', $data['marital_status'] ?? 'never_married');
                        $hasKidsRaw = old('has_children', $data['has_children'] ?? null);
                        $hasKids = $hasKidsRaw === null || $hasKidsRaw === '' ? '' : ($hasKidsRaw ? '1' : '0');
                        $livingVal = old('living_situation', $data['living_situation'] ?? '');
                    @endphp
                    <x-nikah-section :title="__('db.Basic Information')" icon="🧍" color="blue">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ maritalStatus: '{{ $ms }}', hasChildren: '{{ $hasKids }}' }">
                            <div>
                                <x-input-label for="date_of_birth" :value="__('db.Date of Birth')" />
                                <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="w-full mt-1"
                                    :value="old('date_of_birth', $data['date_of_birth'] ?? '')" required
                                    max="{{ now()->subYears(18)->toDateString() }}" min="{{ now()->subYears(100)->toDateString() }}"
                                    title="{{ __('db.You must be at least 18 years old to create a Nikah profile.') }}" />
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
                            @endphp
                            <div>
                                <x-input-label for="height" :value="__('db.Height')" />
                                <x-searchable-select name="height" :options="$heightOptions" :value="old('height', $data['height'] ?? '')"
                                    placeholder="{{ __('db.Type to search, scroll, or pick from the list') }}" title="{{ __('db.Optional — a fixed list keeps this consistent for matching, but you can type any value.') }}" />
                            </div>
                            <div>
                                <x-input-label for="marital_status" :value="__('db.Marital Status')" />
                                <select id="marital_status" name="marital_status" required x-model="maritalStatus" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Please be honest — this matters for compatibility and is checked against your CNIC where relevant.') }}">
                                    <option value="never_married" {{ $ms === 'never_married' ? 'selected' : '' }}>{{ __('db.Never Married') }}</option>
                                    <option value="divorced" {{ $ms === 'divorced' ? 'selected' : '' }}>{{ __('db.Divorced') }}</option>
                                    <option value="widowed" {{ $ms === 'widowed' ? 'selected' : '' }}>{{ __('db.Widowed') }}</option>
                                    <option value="separated" {{ $ms === 'separated' ? 'selected' : '' }}>{{ __('db.Separated') }}</option>
                                    <option value="married" {{ $ms === 'married' ? 'selected' : '' }}>{{ __('db.Married') }}</option>
                                </select>
                            </div>
                            <div x-show="['divorced','widowed','separated'].includes(maritalStatus)" x-cloak>
                                <x-input-label for="has_children" :value="__('db.Do You Have Children?')" />
                                <select id="has_children" name="has_children" x-model="hasChildren" x-bind:required="['divorced','widowed','separated'].includes(maritalStatus)" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">{{ __('db.Select') }}</option>
                                    <option value="1" {{ $hasKids === '1' ? 'selected' : '' }}>{{ __('db.Yes') }}</option>
                                    <option value="0" {{ $hasKids === '0' ? 'selected' : '' }}>{{ __('db.No') }}</option>
                                </select>
                            </div>
                            <div x-show="['divorced','widowed','separated'].includes(maritalStatus) && hasChildren === '1'" x-cloak>
                                <x-input-label for="children_count" :value="__('db.Number of Children')" />
                                <x-text-input id="children_count" name="children_count" type="number" min="1" max="20" class="w-full mt-1"
                                    :value="old('children_count', $data['children_count'] ?? '')" />
                            </div>
                            <div x-show="['divorced','widowed','separated'].includes(maritalStatus)" x-cloak>
                                <x-input-label for="living_situation" :value="__('db.Who Do You Currently Live With?')" />
                                <select id="living_situation" name="living_situation" x-bind:required="['divorced','widowed','separated'].includes(maritalStatus)" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">{{ __('db.Select') }}</option>
                                    <option value="alone" {{ $livingVal === 'alone' ? 'selected' : '' }}>{{ __('db.Alone') }}</option>
                                    <option value="with_parents" {{ $livingVal === 'with_parents' ? 'selected' : '' }}>{{ __('db.With Parents') }}</option>
                                    <option value="with_children" {{ $livingVal === 'with_children' ? 'selected' : '' }}>{{ __('db.With My Children') }}</option>
                                    <option value="with_family" {{ $livingVal === 'with_family' ? 'selected' : '' }}>{{ __('db.With Extended Family') }}</option>
                                    <option value="other" {{ $livingVal === 'other' ? 'selected' : '' }}>{{ __('db.Other') }}</option>
                                </select>
                            </div>
                            @php $educationLevels = ['Matric / O-Levels', 'Intermediate / A-Levels', "Bachelor's", "Master's", 'MPhil / MS', 'PhD', 'Madrassah / Islamic Education']; @endphp
                            <div>
                                <x-input-label for="education" :value="__('db.Education')" />
                                <x-searchable-select name="education" :options="$educationLevels" :value="old('education', $data['education'] ?? '')"
                                    placeholder="{{ __('db.Type to search, scroll, or pick from the list') }}" title="{{ __('db.Start typing to filter, scroll the list, or click one to pick it — you can also type something not listed.') }}" />
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
                            <x-country-state-fields
                                :country-value="old('country', $data['country'] ?? 'Pakistan')"
                                :state-value="old('state', $data['state'] ?? '')"
                                :all-states="$countryStates" />
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
