<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.nikah.verifications') }}" class="text-gray-400 hover:text-gray-600">Nikah Profiles</a>
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

                <form method="POST" action="{{ route('admin.nikah.profiles.create.step.save', 'basic') }}" class="space-y-6">
                    @csrf

                    @php
                        $ms = old('marital_status', $data['marital_status'] ?? '');
                        $hasKidsRaw = old('has_children', $data['has_children'] ?? null);
                        $hasKids = $hasKidsRaw === null || $hasKidsRaw === '' ? '' : ($hasKidsRaw ? '1' : '0');
                        $livingVal = old('living_situation', $data['living_situation'] ?? '');
                    @endphp
                    <x-nikah-section title="Basic Information" icon="🧍" color="blue">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ maritalStatus: '{{ $ms }}', hasChildren: '{{ $hasKids }}' }">
                            <div>
                                <x-input-label for="date_of_birth" value="Date of Birth" />
                                <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="w-full mt-1"
                                    :value="old('date_of_birth', $data['date_of_birth'] ?? '')" required
                                    max="{{ now()->subYears(18)->toDateString() }}" min="{{ now()->subYears(100)->toDateString() }}" />
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
                                <x-input-label for="height" value="Height (optional)" />
                                <x-searchable-select name="height" :options="$heightOptions" :value="old('height', $data['height'] ?? '')" placeholder="Type to search or pick" />
                            </div>
                            <div>
                                <x-input-label for="marital_status" value="Marital Status" />
                                <select id="marital_status" name="marital_status" required x-model="maritalStatus" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Select</option>
                                    @foreach (['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'separated' => 'Separated', 'married' => 'Married (Second Wife)'] as $val => $label)
                                    <option value="{{ $val }}" {{ $ms === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="['divorced','widowed','separated','married'].includes(maritalStatus)" x-cloak>
                                <x-input-label for="has_children" value="Do They Have Children?" />
                                <select id="has_children" name="has_children" x-model="hasChildren" x-bind:required="['divorced','widowed','separated','married'].includes(maritalStatus)" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Select</option>
                                    <option value="1" {{ $hasKids === '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $hasKids === '0' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div x-show="['divorced','widowed','separated','married'].includes(maritalStatus) && hasChildren === '1'" x-cloak>
                                <x-input-label for="children_count" value="Number of Children" />
                                <x-text-input id="children_count" name="children_count" type="number" min="1" max="20" class="w-full mt-1"
                                    :value="old('children_count', $data['children_count'] ?? '')" />
                            </div>
                            <div x-show="['divorced','widowed','separated','married'].includes(maritalStatus)" x-cloak>
                                <x-input-label for="living_situation" value="Who Do They Currently Live With?" />
                                <select id="living_situation" name="living_situation" x-bind:required="['divorced','widowed','separated','married'].includes(maritalStatus)" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Select</option>
                                    <option value="alone" {{ $livingVal === 'alone' ? 'selected' : '' }}>Alone</option>
                                    <option value="with_mother" {{ $livingVal === 'with_mother' ? 'selected' : '' }}>With Mother</option>
                                    <option value="with_father" {{ $livingVal === 'with_father' ? 'selected' : '' }}>With Father</option>
                                    <option value="with_maternal_grandparents" {{ $livingVal === 'with_maternal_grandparents' ? 'selected' : '' }}>With Maternal Grandparents</option>
                                    <option value="with_paternal_grandparents" {{ $livingVal === 'with_paternal_grandparents' ? 'selected' : '' }}>With Paternal Grandparents</option>
                                    <option value="with_children" {{ $livingVal === 'with_children' ? 'selected' : '' }}>With Their Children</option>
                                    <option value="other" {{ $livingVal === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            @php $educationLevels = ['Matric / O-Levels', 'Intermediate / A-Levels', "Bachelor's", "Master's", 'MPhil / MS', 'PhD', 'Madrassah / Islamic Education']; @endphp
                            <div>
                                <x-input-label for="education" value="Education (optional)" />
                                <x-searchable-select name="education" :options="$educationLevels" :value="old('education', $data['education'] ?? '')" placeholder="Type to search or pick" />
                            </div>
                            <div>
                                <x-input-label for="profession" value="Profession (optional)" />
                                <x-text-input id="profession" name="profession" type="text" class="w-full mt-1" :value="old('profession', $data['profession'] ?? '')" />
                            </div>
                            <div>
                                <x-input-label for="city" value="City" />
                                <x-text-input id="city" name="city" type="text" class="w-full mt-1" :value="old('city', $data['city'] ?? '')" required />
                            </div>
                            <x-country-state-fields
                                :country-value="old('country', $data['country'] ?? 'Pakistan')"
                                :state-value="old('state', $data['state'] ?? '')"
                                :all-states="$countryStates" />
                        </div>
                    </x-nikah-section>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route('admin.nikah.profiles.create.step', 'account') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← Back</a>
                        <x-primary-button>Next: Family & Guardian →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-admin-layout>
