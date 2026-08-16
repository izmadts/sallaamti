<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.Edit Your Nikah Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="text-sm text-gray-500 mb-6">{{ __('db.Keep your profile up to date — a complete, honest profile gets better matches.') }}</p>

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <p class="font-medium text-sm mb-1">{{ __('db.Please fix the following before saving:') }}</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('nikah.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <x-nikah-section :title="__('db.Basic Information')" icon="🧍" color="blue">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="age" value="Age" />
                                <x-text-input id="age" name="age" type="number" class="w-full mt-1" :value="old('age', $profile->age)" required
                                    placeholder="{{ __('db.e.g. 27') }}" title="{{ __('db.Your current age in years — must be 18 or older.') }}" />
                            </div>
                            <div>
                                <x-input-label for="gender" :value="__('db.Gender')" />
                                @php $genderVal = old('gender', $profile->user->gender); @endphp
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
                                $heightVal = old('height', $profile->height);
                                $heightIsOther = $heightVal && !in_array($heightVal, $heightOptions);
                            @endphp
                            <div x-data="{ ht: '{{ $heightIsOther ? 'Other' : $heightVal }}' }">
                                <x-input-label for="height" value="Height" />
                                <select id="height" name="height" x-model="ht" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Optional — a fixed list keeps this consistent for matching.') }}">
                                    <option value="">{{ __('db.Prefer not to say') }}</option>
                                    @foreach ($heightOptions as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                    @endforeach
                                    <option value="Other">{{ __('db.Other') }}</option>
                                </select>
                                <div x-show="ht === 'Other'" x-cloak class="mt-2">
                                    <x-text-input name="height_other" type="text" class="w-full" placeholder="{{ __('db.e.g. 173cm') }}" :value="old('height_other', $heightIsOther ? $heightVal : '')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="marital_status" value="Marital Status" />
                                <select id="marital_status" name="marital_status" required class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Please be honest — this matters for compatibility and is checked against your CNIC where relevant.') }}">
                                    @foreach (['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'separated' => 'Separated', 'married' => 'Married (Second Wife)'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('marital_status', $profile->marital_status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @php
                                $knownSects = ['Sunni', 'Shia', 'Ahle Hadith', 'Deobandi'];
                                $currentSect = old('sect', $profile->sect);
                                $isOtherSect = $currentSect && !in_array($currentSect, $knownSects);
                            @endphp
                            <div x-data="{ sect: '{{ $isOtherSect ? 'Other' : $currentSect }}' }">
                                <x-input-label for="sect" value="Sect" />
                                <select id="sect" name="sect" x-model="sect" required class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Your school of thought — helps us match you with someone compatible.') }}">
                                    <option value="">Select Sect</option>
                                    <option value="Sunni">Sunni</option>
                                    <option value="Shia">Shia</option>
                                    <option value="Ahle Hadith">Ahle Hadith</option>
                                    <option value="Deobandi">Deobandi</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div x-show="sect === 'Other'" x-cloak class="mt-2">
                                    <x-text-input name="sect_other" type="text" class="w-full" placeholder="Please specify your sect" :value="old('sect_other', $isOtherSect ? $currentSect : '')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="caste" value="Caste (optional)" />
                                <x-text-input id="caste" name="caste" type="text" class="w-full mt-1" :value="old('caste', $profile->caste)"
                                    placeholder="{{ __('db.e.g. Rajput, Sheikh, Syed') }}" title="{{ __('db.Only if caste/biradari matters to your family — leave blank if not.') }}" />
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="open_to_polygamy" name="open_to_polygamy" value="1" {{ old('open_to_polygamy', $profile->open_to_polygamy) ? 'checked' : '' }} class="rounded"
                                title="{{ __('db.Only check this if it genuinely applies to you — visible to everyone who views your profile.') }}">
                            <x-input-label for="open_to_polygamy" value="I am open to a polygamous marriage (e.g. as/marrying a second wife)" />
                        </div>
                    </x-nikah-section>

                    <x-nikah-section :title="__('db.Deen & Lifestyle')" icon="🕌" color="emerald">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="prayer_frequency" value="Prayer (Salah) Regularity" />
                                <select id="prayer_frequency" name="prayer_frequency" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.How regularly you pray — helps match people at a similar stage of practice.') }}">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['always' => 'Always — 5 times a day', 'usually' => 'Usually', 'sometimes' => 'Sometimes', 'rarely' => 'Rarely'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('prayer_frequency', $profile->prayer_frequency) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="hijab_or_beard" value="Hijab (for sisters) / Beard (for brothers)" />
                                <select id="hijab_or_beard" name="hijab_or_beard" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Sisters: do you wear hijab? Brothers: do you keep a beard?') }}">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['yes' => 'Yes', 'sometimes' => 'Sometimes', 'no' => 'No'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('hijab_or_beard', $profile->hijab_or_beard) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="smokes" value="Smoking" />
                                <select id="smokes" name="smokes" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Be honest — many members filter matches by this.') }}">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['no' => 'No', 'occasionally' => 'Occasionally', 'yes' => 'Yes'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('smokes', $profile->smokes) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="diet" value="Diet" />
                                <select id="diet" name="diet" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.How strict you are about halal food.') }}">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['halal_only' => 'Halal only', 'halal_mostly' => 'Halal, mostly', 'no_restriction' => 'No restriction'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('diet', $profile->diet) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section :title="__('db.Education & Profession')" icon="🎓" color="indigo">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @php
                                $educationLevels = ['Matric / O-Levels', 'Intermediate / A-Levels', "Bachelor's", "Master's", 'MPhil / MS', 'PhD', 'Madrassah / Islamic Education'];
                                $educationVal = old('education', $profile->education);
                                $educationIsOther = $educationVal && !in_array($educationVal, $educationLevels);
                            @endphp
                            <div x-data="{ edu: '{{ $educationIsOther ? 'Other' : $educationVal }}' }">
                                <x-input-label for="education" value="Education" />
                                <select id="education" name="education" x-model="edu" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Your highest completed education level.') }}">
                                    <option value="">{{ __('db.Prefer not to say') }}</option>
                                    @foreach ($educationLevels as $lvl)
                                    <option value="{{ $lvl }}">{{ __('db.' . $lvl) }}</option>
                                    @endforeach
                                    <option value="Other">{{ __('db.Other') }}</option>
                                </select>
                                <div x-show="edu === 'Other'" x-cloak class="mt-2">
                                    <x-text-input name="education_other" type="text" class="w-full" placeholder="{{ __('db.e.g. Diploma in Nursing') }}" :value="old('education_other', $educationIsOther ? $educationVal : '')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="profession" value="Profession" />
                                <x-text-input id="profession" name="profession" type="text" class="w-full mt-1" :value="old('profession', $profile->profession)"
                                    placeholder="{{ __('db.e.g. Software Engineer, Teacher, Doctor') }}" title="{{ __('db.What you currently do for work.') }}" />
                            </div>
                            <div>
                                <x-input-label for="city" value="City" />
                                <x-text-input id="city" name="city" type="text" class="w-full mt-1" :value="old('city', $profile->city ?: auth()->user()->city)" required
                                    placeholder="{{ __('db.e.g. Karachi, Lahore, Islamabad') }}" title="{{ __('db.The city you currently live in.') }}" />
                            </div>
                            <div>
                                <x-input-label for="country" value="Country" />
                                <x-text-input id="country" name="country" type="text" class="w-full mt-1" :value="old('country', $profile->country)"
                                    placeholder="{{ __('db.e.g. Pakistan') }}" title="{{ __('db.The country you currently live in.') }}" />
                            </div>
                            <div>
                                <x-input-label for="ethnicity" value="Ethnicity (optional)" />
                                <x-text-input id="ethnicity" name="ethnicity" type="text" class="w-full mt-1" :value="old('ethnicity', $profile->ethnicity)" placeholder="e.g. Punjabi, Pashtun, Sindhi, Muhajir"
                                    title="{{ __('db.Your ethnic background, if you\'d like to share it.') }}" />
                            </div>
                            @php
                                $commonLanguages = ['Urdu', 'English', 'Punjabi', 'Pashto', 'Sindhi', 'Saraiki', 'Balochi'];
                                if (old('languages') !== null || old('language_other') !== null) {
                                    $checkedLanguages = old('languages', []);
                                    $otherLanguageText = old('language_other', '');
                                } else {
                                    $profileLangParts = array_filter(array_map('trim', explode(',', $profile->language ?? '')));
                                    $checkedLanguages = array_intersect($profileLangParts, $commonLanguages);
                                    $otherLanguageText = implode(', ', array_diff($profileLangParts, $commonLanguages));
                                }
                            @endphp
                            <div class="sm:col-span-2" x-data="{ other: {{ $otherLanguageText ? 'true' : 'false' }} }">
                                <x-input-label value="Language(s) Spoken (optional)" />
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach ($commonLanguages as $lang)
                                    <label class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-full border cursor-pointer transition hover:bg-gray-50" style="border-color: #d1d5db">
                                        <input type="checkbox" name="languages[]" value="{{ $lang }}" {{ in_array($lang, $checkedLanguages) ? 'checked' : '' }} class="rounded">
                                        {{ __('db.' . $lang) }}
                                    </label>
                                    @endforeach
                                    <label class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-full border cursor-pointer transition hover:bg-gray-50" style="border-color: #d1d5db">
                                        <input type="checkbox" x-model="other" class="rounded">
                                        {{ __('db.Other') }}
                                    </label>
                                </div>
                                <div x-show="other" x-cloak class="mt-2">
                                    <x-text-input name="language_other" type="text" class="w-full" placeholder="{{ __('db.e.g. Balti, Hindko, Arabic') }}" value="{{ $otherLanguageText }}" />
                                </div>
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section :title="__('db.Family & Guardian Information')" icon="👨‍👩‍👧" color="amber">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @php
                                $familyTypeOptions = ['Joint Family', 'Nuclear Family', 'Living with In-Laws'];
                                $familyTypeVal = old('family_type', $profile->family_type);
                                $familyTypeIsOther = $familyTypeVal && !in_array($familyTypeVal, $familyTypeOptions);
                            @endphp
                            <div x-data="{ ft: '{{ $familyTypeIsOther ? 'Other' : $familyTypeVal }}' }">
                                <x-input-label for="family_type" value="Family Type" />
                                <select id="family_type" name="family_type" x-model="ft" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Do you live in a joint family or on your own as a separate household?') }}">
                                    <option value="">{{ __('db.Prefer not to say') }}</option>
                                    @foreach ($familyTypeOptions as $ft)
                                    <option value="{{ $ft }}">{{ __('db.' . $ft) }}</option>
                                    @endforeach
                                    <option value="Other">{{ __('db.Other') }}</option>
                                </select>
                                <div x-show="ft === 'Other'" x-cloak class="mt-2">
                                    <x-text-input name="family_type_other" type="text" class="w-full" placeholder="{{ __('db.Please describe your family setup') }}" :value="old('family_type_other', $familyTypeIsOther ? $familyTypeVal : '')" />
                                </div>
                            </div>
                            @php
                                $guardianRelOptions = ['Father', 'Mother', 'Brother', 'Sister', 'Uncle', 'Aunt', 'Grandfather', 'Grandmother'];
                                $guardianRelVal = old('guardian_relation', $profile->guardian_relation);
                                $guardianRelIsOther = $guardianRelVal && !in_array($guardianRelVal, $guardianRelOptions);
                            @endphp
                            <div x-data="{ rel: '{{ $guardianRelIsOther ? 'Other' : $guardianRelVal }}' }">
                                <x-input-label for="guardian_relation" value="Guardian Relation" />
                                <select id="guardian_relation" name="guardian_relation" x-model="rel" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    title="{{ __('db.Your relationship to the guardian named below.') }}">
                                    <option value="">{{ __('db.Select') }}</option>
                                    @foreach ($guardianRelOptions as $rel)
                                    <option value="{{ $rel }}">{{ __('db.' . $rel) }}</option>
                                    @endforeach
                                    <option value="Other">{{ __('db.Other') }}</option>
                                </select>
                                <div x-show="rel === 'Other'" x-cloak class="mt-2">
                                    <x-text-input name="guardian_relation_other" type="text" class="w-full" placeholder="{{ __('db.e.g. Cousin, Family Friend') }}" :value="old('guardian_relation_other', $guardianRelIsOther ? $guardianRelVal : '')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="guardian_name" value="Guardian Name" />
                                <x-text-input id="guardian_name" name="guardian_name" type="text" class="w-full mt-1" :value="old('guardian_name', $profile->guardian_name)" required
                                    placeholder="{{ __('db.e.g. Muhammad Ahmed') }}" title="{{ __('db.Your Wali/guardian\'s full name — so we can involve your family in the process.') }}" />
                            </div>
                            <div>
                                <x-input-label for="guardian_contact" value="Guardian Contact" />
                                <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="w-full mt-1" :value="old('guardian_contact', $profile->guardian_contact)" required
                                    placeholder="{{ __('db.e.g. 03001234567') }}" title="{{ __('db.A number where we or an interested family can reach your guardian.') }}" />
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section :title="__('db.About')" icon="💬" color="purple">
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="about" value="About Yourself" />
                                <textarea id="about" name="about" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    placeholder="{{ __('db.Tell us about your personality, values, daily routine, and what makes you, you...') }}"
                                    title="{{ __('db.This is what other members read first — a few honest sentences go a long way.') }}">{{ old('about', $profile->about) }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="expectations" value="Looking For" />
                                <textarea id="expectations" name="expectations" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    placeholder="{{ __('db.e.g. Someone practicing, family-oriented, kind-hearted...') }}"
                                    title="{{ __('db.What qualities matter most to you in a spouse.') }}">{{ old('expectations', $profile->expectations) }}</textarea>
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section :title="__('db.Verification')" icon="🪪" color="rose" :description="__('db.Your CNIC is private and never shown publicly.')">
                        <div>
                            @if ($profile->cnic_number && $profile->cnic_front_image && $profile->cnic_back_image)
                            <div class="bg-white border border-rose-200 rounded-lg p-4 text-sm text-gray-600">
                                <p class="font-medium text-gray-700 mb-1">🔒 CNIC Verification Submitted</p>
                                <p>CNIC Number: {{ $profile->cnic_number }}</p>
                                <p class="mt-1">Your CNIC details are locked and cannot be changed here. If you need to update your CNIC, please contact support.</p>
                            </div>
                            @else
                            <p class="text-sm text-gray-500 mb-3">Leave file fields blank to keep your current uploads.</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @if (!$profile->cnic_number)
                                <div>
                                    <x-input-label for="cnic_number" value="CNIC Number" />
                                    <x-text-input id="cnic_number" name="cnic_number" type="text" class="w-full mt-1" :value="old('cnic_number')" required
                                        placeholder="{{ __('db.e.g. 12345-1234567-1') }}" title="{{ __('db.Your 13-digit CNIC number, exactly as printed on your card.') }}" />
                                    <p class="text-xs text-gray-400 mt-1">{{ __('db.Must be unique — one CNIC can only be used for one profile.') }}</p>
                                </div>
                                @endif
                                @if (!$profile->cnic_front_image)
                                <div>
                                    <x-input-label for="cnic_front_image" value="CNIC Photo (Front)" />
                                    <input id="cnic_front_image" name="cnic_front_image" type="file" accept="image/*" capture="environment" class="w-full mt-1" required
                                        title="{{ __('db.On a phone this opens your camera directly — or choose an existing photo from your gallery.') }}">
                                    <p class="text-[11px] text-gray-400 mt-1">📷 {{ __('db.On mobile, tap to take the photo directly with your camera.') }}</p>
                                </div>
                                @endif
                                @if (!$profile->cnic_back_image)
                                <div>
                                    <x-input-label for="cnic_back_image" value="CNIC Photo (Back)" />
                                    <input id="cnic_back_image" name="cnic_back_image" type="file" accept="image/*" capture="environment" class="w-full mt-1" required
                                        title="{{ __('db.On a phone this opens your camera directly — or choose an existing photo from your gallery.') }}">
                                    <p class="text-[11px] text-gray-400 mt-1">📷 {{ __('db.On mobile, tap to take the photo directly with your camera.') }}</p>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                        <hr class="mt-4 border-rose-200">
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="allow_photo_sharing" name="allow_photo_sharing" value="1" {{ old('allow_photo_sharing', $profile->allow_photo_sharing) ? 'checked' : '' }} class="rounded"
                                title="{{ __('db.Your photo stays private until both sides accept interest in each other — this just decides what happens after that.') }}">
                            <x-input-label for="allow_photo_sharing" value="Allow my photo to be shared with a match after mutual interest is accepted" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="photo" value="Your Photo (replace)" />
                            <input id="photo" name="photo" type="file" accept="image/*" class="w-full mt-1"
                                title="{{ __('db.A clear, recent photo of yourself — kept private and only shown after mutual interest is accepted.') }}" />
                        </div>
                    </x-nikah-section>

                    <x-nikah-section :title="__('db.Profile Visibility')" icon="👁️" color="teal">
                        <select id="visibility" name="visibility" required class="border-gray-300 rounded-md shadow-sm w-full"
                            title="{{ __('db.You can change this anytime.') }}">
                            <option value="public" {{ old('visibility', $profile->visibility) === 'public' ? 'selected' : '' }}>Public (visible in Browse Matches search)</option>
                            <option value="private" {{ old('visibility', $profile->visibility) === 'private' ? 'selected' : '' }}>Private (hidden from search — only visible via your share link)</option>
                        </select>
                    </x-nikah-section>

                    <x-nikah-section :title="__('db.Match Preferences')" icon="🎯" color="sky" :description="__('db.Sharing these improves your match %.')">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="pref_min_age" value="Preferred Min Age" />
                                <x-text-input id="pref_min_age" name="pref_min_age" type="number" class="w-full mt-1" :value="old('pref_min_age', $profile->pref_min_age)"
                                    placeholder="{{ __('db.e.g. 22') }}" title="{{ __('db.Leave blank for no minimum.') }}" />
                            </div>
                            <div>
                                <x-input-label value="Preferred Max Age" />
                                <x-text-input name="pref_max_age" type="number" class="w-full mt-1" :value="old('pref_max_age', $profile->pref_max_age)"
                                    placeholder="{{ __('db.e.g. 30') }}" title="{{ __('db.Leave blank for no maximum.') }}" />
                            </div>
                            <div>
                                <x-input-label value="Preferred City" />
                                <x-text-input name="pref_city" class="w-full mt-1" :value="old('pref_city', $profile->pref_city)"
                                    placeholder="{{ __('db.e.g. Karachi (leave blank for any city)') }}" title="{{ __('db.Leave blank if the city doesn\'t matter to you.') }}" />
                            </div>
                            <div>
                                <x-input-label value="Preferred Sect" />
                                <x-text-input name="pref_sect" class="w-full mt-1" :value="old('pref_sect', $profile->pref_sect)"
                                    placeholder="{{ __('db.e.g. Sunni (leave blank for any)') }}" title="{{ __('db.Leave blank if sect doesn\'t matter to you.') }}" />
                            </div>
                            <div>
                                <x-input-label value="Preferred Education Level" />
                                <x-text-input name="pref_education" class="w-full mt-1" :value="old('pref_education', $profile->pref_education)"
                                    placeholder="{{ __('db.e.g. Graduate or above (leave blank for any)') }}" title="{{ __('db.Leave blank if education level doesn\'t matter to you.') }}" />
                            </div>
                            <div>
                                <x-input-label value="Preferred Marital Status" />
                                <select name="pref_marital_status" class="border-gray-300 rounded-md w-full mt-1"
                                    title="{{ __('db.Leave as No preference if this doesn\'t matter to you.') }}">
                                    <option value="">No preference</option>
                                    <option value="never_married" {{ old('pref_marital_status', $profile->pref_marital_status) === 'never_married' ? 'selected' : '' }}>Never Married</option>
                                    <option value="divorced" {{ old('pref_marital_status', $profile->pref_marital_status) === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="widowed" {{ old('pref_marital_status', $profile->pref_marital_status) === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                            </div>
                        </div>
                    </x-nikah-section>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('nikah.show') }}" class="px-4 py-2 text-sm text-gray-600">Cancel</a>
                        <x-primary-button>{{ __('db.Save My Profile') }}</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Jumps straight to whichever field the "Complete Your Nikah Profile"
         banner linked to (via #anchor) instead of just landing at the top
         of a long form, and briefly highlights it so it's unmistakable. --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.location.hash) return;
            const target = document.querySelector(window.location.hash);
            if (!target) return;

            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            target.classList.add('ring-2', 'ring-amber-400', 'ring-offset-2');
            if (typeof target.focus === 'function') target.focus({ preventScroll: true });
            setTimeout(() => target.classList.remove('ring-2', 'ring-amber-400', 'ring-offset-2'), 2500);
        });
    </script>
</x-app-layout>
