<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Your Nikah Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('nikah.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Basic Info -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Basic Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="age" value="Age" />
                                <x-text-input id="age" name="age" type="number" class="w-full mt-1" :value="old('age')" required />
                            </div>
                            <div>
                                <x-input-label for="height" value="Height (e.g. 5'8&quot;)" />
                                <x-text-input id="height" name="height" type="text" class="w-full mt-1" :value="old('height')" />
                            </div>
                            <div>
                                <x-input-label for="marital_status" value="Marital Status" />
                                <select id="marital_status" name="marital_status" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="never_married">Never Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="widowed">Widowed</option>
                                    <option value="separated">Separated</option>
                                    <option value="married">Married</option>
                                </select>
                            </div>
                            <div x-data="{ sect: '{{ old('sect', '') }}' }">
                                <x-input-label for="sect" value="Sect" />
                                <select id="sect" name="sect" x-model="sect" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Select Sect</option>
                                    <option value="Sunni">Sunni</option>
                                    <option value="Shia">Shia</option>
                                    <option value="Ahle Hadith">Ahle Hadith</option>
                                    <option value="Deobandi">Deobandi</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div x-show="sect === 'Other'" x-cloak class="mt-2">
                                    <x-text-input name="sect_other" type="text" class="w-full" placeholder="Please specify your sect" :value="old('sect_other')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="caste" value="Caste (optional)" />
                                <x-text-input id="caste" name="caste" type="text" class="w-full mt-1" :value="old('caste')" />
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="open_to_polygamy" name="open_to_polygamy" value="1" {{ old('open_to_polygamy') ? 'checked' : '' }} class="rounded">
                            <x-input-label for="open_to_polygamy" value="I am open to a polygamous marriage (e.g. as/marrying a second wife)" />
                        </div>
                    </div>

                    <!-- Deen & Lifestyle -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Deen & Lifestyle</h3>
                        <p class="text-sm text-gray-500 mb-3">These help us match you with someone at a similar stage of practice — all optional, but the more you share, the better your matches.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="prayer_frequency" value="Prayer (Salah) Regularity" />
                                <select id="prayer_frequency" name="prayer_frequency" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    <option value="always" {{ old('prayer_frequency') === 'always' ? 'selected' : '' }}>Always — 5 times a day</option>
                                    <option value="usually" {{ old('prayer_frequency') === 'usually' ? 'selected' : '' }}>Usually</option>
                                    <option value="sometimes" {{ old('prayer_frequency') === 'sometimes' ? 'selected' : '' }}>Sometimes</option>
                                    <option value="rarely" {{ old('prayer_frequency') === 'rarely' ? 'selected' : '' }}>Rarely</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="hijab_or_beard" value="Hijab (for sisters) / Beard (for brothers)" />
                                <select id="hijab_or_beard" name="hijab_or_beard" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    <option value="yes" {{ old('hijab_or_beard') === 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="sometimes" {{ old('hijab_or_beard') === 'sometimes' ? 'selected' : '' }}>Sometimes</option>
                                    <option value="no" {{ old('hijab_or_beard') === 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="smokes" value="Smoking" />
                                <select id="smokes" name="smokes" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    <option value="no" {{ old('smokes') === 'no' ? 'selected' : '' }}>No</option>
                                    <option value="occasionally" {{ old('smokes') === 'occasionally' ? 'selected' : '' }}>Occasionally</option>
                                    <option value="yes" {{ old('smokes') === 'yes' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="diet" value="Diet" />
                                <select id="diet" name="diet" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    <option value="halal_only" {{ old('diet') === 'halal_only' ? 'selected' : '' }}>Halal only</option>
                                    <option value="halal_mostly" {{ old('diet') === 'halal_mostly' ? 'selected' : '' }}>Halal, mostly</option>
                                    <option value="no_restriction" {{ old('diet') === 'no_restriction' ? 'selected' : '' }}>No restriction</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Education & Profession -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Education & Profession</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="education" value="Education" />
                                <x-text-input id="education" name="education" type="text" class="w-full mt-1" :value="old('education')" />
                            </div>
                            <div>
                                <x-input-label for="profession" value="Profession" />
                                <x-text-input id="profession" name="profession" type="text" class="w-full mt-1" :value="old('profession')" />
                            </div>
                            <div>
                                <x-input-label for="city" value="City" />
                                <x-text-input id="city" name="city" type="text" class="w-full mt-1" :value="old('city')" required />
                            </div>
                            <div>
                                <x-input-label for="country" value="Country" />
                                <x-text-input id="country" name="country" type="text" class="w-full mt-1" :value="old('country', 'Pakistan')" />
                            </div>
                            <div>
                                <x-input-label for="ethnicity" value="Ethnicity (optional)" />
                                <x-text-input id="ethnicity" name="ethnicity" type="text" class="w-full mt-1" :value="old('ethnicity')" placeholder="e.g. Punjabi, Pashtun, Sindhi" />
                            </div>
                            <div>
                                <x-input-label for="language" value="Language(s) Spoken (optional)" />
                                <x-text-input id="language" name="language" type="text" class="w-full mt-1" :value="old('language')" placeholder="e.g. Urdu, English" />
                            </div>
                        </div>
                    </div>

                    <!-- Family / Guardian Info -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Family & Guardian Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="family_type" value="Family Type" />
                                <x-text-input id="family_type" name="family_type" type="text" class="w-full mt-1" :value="old('family_type')" placeholder="Joint / Nuclear" />
                            </div>
                            <div>
                                <x-input-label for="guardian_relation" value="Guardian Relation" />
                                <x-text-input id="guardian_relation" name="guardian_relation" type="text" class="w-full mt-1" :value="old('guardian_relation')" placeholder="Father / Brother" />
                            </div>
                            <div>
                                <x-input-label for="guardian_name" value="Guardian Name" />
                                <x-text-input id="guardian_name" name="guardian_name" type="text" class="w-full mt-1" :value="old('guardian_name')" required />
                            </div>
                            <div>
                                <x-input-label for="guardian_contact" value="Guardian Contact Number" />
                                <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="w-full mt-1" :value="old('guardian_contact')" required />
                            </div>
                        </div>
                    </div>

                    <!-- About -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">About</h3>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="about" value="About Yourself" />
                                <textarea id="about" name="about" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('about') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="expectations" value="What are you looking for in a partner?" />
                                <textarea id="expectations" name="expectations" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('expectations') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Verification -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Verification (Required)</h3>
                        <p class="text-sm text-gray-500 mb-3">Your CNIC will only be used for verification and is never shown publicly.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="cnic_number" value="CNIC Number" />
                                <x-text-input id="cnic_number" name="cnic_number" type="text" class="w-full mt-1" :value="old('cnic_number')" required />
                            </div>
                            <div></div>
                            <div>
                                <x-input-label for="cnic_front_image" value="CNIC Photo (Front)" />
                                <input id="cnic_front_image" name="cnic_front_image" type="file" accept="image/*" class="w-full mt-1" required>
                            </div>
                            <div>
                                <x-input-label for="cnic_back_image" value="CNIC Photo (Back)" />
                                <input id="cnic_back_image" name="cnic_back_image" type="file" accept="image/*" class="w-full mt-1" required>
                            </div>
                        </div>
                        <hr class="mt-4 border-b">
                        <div class="mt-4">
                            <x-input-label for="photo" value="Your Nikah Profile Photo (private, shared only if you allow) hidden until mutual interest is accepted)" />
                            <input id="photo" name="photo" type="file" accept="image/*" class="w-full mt-1" />
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="allow_photo_sharing" name="allow_photo_sharing" value="1" checked class="rounded">
                            <x-input-label for="allow_photo_sharing" value="Allow my photo to be shared with a match after mutual interest is accepted" />
                        </div>
                    </div>

                    <!-- Visibility -->
                    <div>
                        <x-input-label for="visibility" value="Profile Visibility" />
                        <select id="visibility" name="visibility" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                            <option value="public">Public (visible in Browse Matches search after verification)</option>
                            <option value="private">Private (hidden from search — only visible to people you personally send your share link to)</option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Submit Profile</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>