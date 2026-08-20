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

                <p class="text-sm text-gray-500 mb-6">
                    For walk-in registrants who can't create their own account — this creates their login and Nikah profile together.
                    Held to the same requirements as a self-created profile, including CNIC photos.
                </p>

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

                <form method="POST" action="{{ route('admin.nikah.profiles.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <x-nikah-section title="Login Account" icon="🔑" color="blue" description="An account is created for them — if you give an email, a password-setup link is sent right away.">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="name" value="Full Name" />
                                <x-text-input id="name" name="name" type="text" class="w-full mt-1" :value="old('name')" required />
                            </div>
                            <div>
                                <x-input-label for="identifier" value="Email or Phone Number" />
                                <x-text-input id="identifier" name="identifier" type="text" class="w-full mt-1" :value="old('identifier')" required
                                    placeholder="e.g. name@example.com or 03001234567" title="An email lets us send them a password-setup link immediately. Phone-only accounts need a login link added manually later." />
                            </div>
                            <div>
                                <x-input-label for="gender" value="Gender" />
                                <select id="gender" name="gender" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Select</option>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section title="Basic Information" icon="🧍" color="blue">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="age" value="Age" />
                                <x-text-input id="age" name="age" type="number" class="w-full mt-1" :value="old('age')" required />
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
                                <x-searchable-select name="height" :options="$heightOptions" :value="old('height')" placeholder="Type to search or pick" />
                            </div>
                            <div>
                                <x-input-label for="marital_status" value="Marital Status" />
                                <select id="marital_status" name="marital_status" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    @foreach (['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'separated' => 'Separated', 'married' => 'Married (Second Wife)'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('marital_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-data="{ sect: '{{ old('sect', '') }}' }">
                                <x-input-label for="sect" value="Sect (optional)" />
                                <select id="sect" name="sect" x-model="sect" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Select Sect</option>
                                    <option value="Sunni">Sunni</option>
                                    <option value="Shia">Shia</option>
                                    <option value="Ahle Hadith">Ahle Hadith</option>
                                    <option value="Deobandi">Deobandi</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div x-show="sect === 'Other'" x-cloak class="mt-2">
                                    <x-text-input name="sect_other" type="text" class="w-full" placeholder="Please specify" :value="old('sect_other')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="caste" value="Caste (optional)" />
                                <x-text-input id="caste" name="caste" type="text" class="w-full mt-1" :value="old('caste')" />
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="open_to_polygamy" name="open_to_polygamy" value="1" {{ old('open_to_polygamy') ? 'checked' : '' }} class="rounded">
                            <x-input-label for="open_to_polygamy" value="Open to a polygamous marriage" />
                        </div>
                    </x-nikah-section>

                    <x-nikah-section title="Deen & Lifestyle" icon="🕌" color="emerald">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="prayer_frequency" value="Prayer (Salah) Regularity" />
                                <select id="prayer_frequency" name="prayer_frequency" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['always' => 'Always — 5 times a day', 'usually' => 'Usually', 'sometimes' => 'Sometimes', 'rarely' => 'Rarely'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('prayer_frequency') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="hijab_or_beard" value="Hijab / Beard" />
                                <select id="hijab_or_beard" name="hijab_or_beard" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['yes' => 'Yes', 'sometimes' => 'Sometimes', 'no' => 'No'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('hijab_or_beard') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="smokes" value="Smoking" />
                                <select id="smokes" name="smokes" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['no' => 'No', 'occasionally' => 'Occasionally', 'yes' => 'Yes'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('smokes') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="diet" value="Diet" />
                                <select id="diet" name="diet" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['halal_only' => 'Halal only', 'halal_mostly' => 'Halal, mostly', 'no_restriction' => 'No restriction'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('diet') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section title="Education & Profession" icon="🎓" color="indigo">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @php $educationLevels = ['Matric / O-Levels', 'Intermediate / A-Levels', "Bachelor's", "Master's", 'MPhil / MS', 'PhD', 'Madrassah / Islamic Education']; @endphp
                            <div>
                                <x-input-label for="education" value="Education" />
                                <x-searchable-select name="education" :options="$educationLevels" :value="old('education')" placeholder="Type to search or pick" />
                            </div>
                            <div>
                                <x-input-label for="profession" value="Profession" />
                                <x-text-input id="profession" name="profession" type="text" class="w-full mt-1" :value="old('profession')" />
                            </div>
                            <div>
                                <x-input-label for="city" value="City" />
                                <x-text-input id="city" name="city" type="text" class="w-full mt-1" :value="old('city')" required />
                            </div>
                            <x-country-state-fields
                                :country-value="old('country', 'Pakistan')"
                                :state-value="old('state')"
                                :all-states="$countryStates" />
                            <div>
                                <x-input-label for="ethnicity" value="Ethnicity (optional)" />
                                <x-text-input id="ethnicity" name="ethnicity" type="text" class="w-full mt-1" :value="old('ethnicity')" />
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section title="Family & Guardian Information" icon="👨‍👩‍👧" color="amber">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @php $familyTypeOptions = ['Joint Family', 'Nuclear Family', 'Living with In-Laws']; @endphp
                            <div>
                                <x-input-label for="family_type" value="Family Type (optional)" />
                                <x-searchable-select name="family_type" :options="$familyTypeOptions" :value="old('family_type')" placeholder="Type to search or pick" />
                            </div>
                            @php $guardianRelOptions = ['Self', 'Father', 'Mother', 'Brother', 'Sister', 'Uncle', 'Aunt', 'Grandfather', 'Grandmother']; @endphp
                            <div>
                                <x-input-label for="guardian_relation" value="Guardian Relation (optional)" />
                                <x-searchable-select name="guardian_relation" :options="$guardianRelOptions" :value="old('guardian_relation')" placeholder="Type to search or pick" />
                            </div>
                            <div>
                                <x-input-label for="guardian_name" value="Guardian Name" />
                                <x-text-input id="guardian_name" name="guardian_name" type="text" class="w-full mt-1" :value="old('guardian_name')" required />
                            </div>
                            <div>
                                <x-input-label for="guardian_contact" value="Guardian Contact" />
                                <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="w-full mt-1" :value="old('guardian_contact')" required />
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section title="About" icon="💬" color="purple">
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="about" value="About (optional)" />
                                <textarea id="about" name="about" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('about') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="expectations" value="Looking For (optional)" />
                                <textarea id="expectations" name="expectations" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('expectations') }}</textarea>
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section title="Verification" icon="🪪" color="rose" description="Required — held to the same bar as a self-created profile.">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="cnic_number" value="CNIC Number" />
                                <x-text-input id="cnic_number" name="cnic_number" type="text" class="w-full mt-1" :value="old('cnic_number')" required placeholder="e.g. 12345-1234567-1" />
                            </div>
                            <div>
                                <x-input-label for="cnic_front_image" value="CNIC Photo (Front)" />
                                <x-photo-upload-field name="cnic_front_image" :required="true" />
                            </div>
                            <div>
                                <x-input-label for="cnic_back_image" value="CNIC Photo (Back)" />
                                <x-photo-upload-field name="cnic_back_image" :required="true" />
                            </div>
                            <div>
                                <x-input-label for="photo" value="Profile Photo (optional)" />
                                <input id="photo" name="photo" type="file" accept="image/*" class="w-full mt-1">
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="allow_photo_sharing" name="allow_photo_sharing" value="1" {{ old('allow_photo_sharing') ? 'checked' : '' }} class="rounded">
                            <x-input-label for="allow_photo_sharing" value="Allow photo to be shared with a match after mutual interest is accepted" />
                        </div>
                    </x-nikah-section>

                    <x-nikah-section title="Profile Visibility" icon="👁️" color="teal">
                        <select id="visibility" name="visibility" required class="border-gray-300 rounded-md shadow-sm w-full">
                            <option value="public" {{ old('visibility', 'public') === 'public' ? 'selected' : '' }}>Public (others can browse to it)</option>
                            <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>Private (hidden from search — only via share link)</option>
                        </select>
                    </x-nikah-section>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('admin.nikah.verifications') }}" class="px-4 py-2 text-sm text-gray-600">Cancel</a>
                        <x-primary-button>Create Profile & Account</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-admin-layout>
