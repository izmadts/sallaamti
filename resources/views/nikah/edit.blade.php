<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Your Nikah Profile') }}
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

                <form method="POST" action="{{ route('nikah.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Basic Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="age" value="Age" />
                                <x-text-input id="age" name="age" type="number" class="w-full mt-1" :value="old('age', $profile->age)" required />
                            </div>
                            <div>
                                <x-input-label for="height" value="Height" />
                                <x-text-input id="height" name="height" type="text" class="w-full mt-1" :value="old('height', $profile->height)" />
                            </div>
                            <div>
                                <x-input-label for="marital_status" value="Marital Status" />
                                <select id="marital_status" name="marital_status" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    @foreach (['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'married' => 'Married (Second Wife)'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('marital_status', $profile->marital_status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="sect" value="Sect" />
                                <x-text-input id="sect" name="sect" type="text" class="w-full mt-1" :value="old('sect', $profile->sect)" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Education & Profession</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="education" value="Education" />
                                <x-text-input id="education" name="education" type="text" class="w-full mt-1" :value="old('education', $profile->education)" />
                            </div>
                            <div>
                                <x-input-label for="profession" value="Profession" />
                                <x-text-input id="profession" name="profession" type="text" class="w-full mt-1" :value="old('profession', $profile->profession)" />
                            </div>
                            <div>
                                <x-input-label for="city" value="City" />
                                <x-text-input id="city" name="city" type="text" class="w-full mt-1" :value="old('city', $profile->city)" required />
                            </div>
                            <div>
                                <x-input-label for="country" value="Country" />
                                <x-text-input id="country" name="country" type="text" class="w-full mt-1" :value="old('country', $profile->country)" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Family & Guardian Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="family_type" value="Family Type" />
                                <x-text-input id="family_type" name="family_type" type="text" class="w-full mt-1" :value="old('family_type', $profile->family_type)" />
                            </div>
                            <div>
                                <x-input-label for="guardian_relation" value="Guardian Relation" />
                                <x-text-input id="guardian_relation" name="guardian_relation" type="text" class="w-full mt-1" :value="old('guardian_relation', $profile->guardian_relation)" />
                            </div>
                            <div>
                                <x-input-label for="guardian_name" value="Guardian Name" />
                                <x-text-input id="guardian_name" name="guardian_name" type="text" class="w-full mt-1" :value="old('guardian_name', $profile->guardian_name)" required />
                            </div>
                            <div>
                                <x-input-label for="guardian_contact" value="Guardian Contact" />
                                <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="w-full mt-1" :value="old('guardian_contact', $profile->guardian_contact)" required />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">About</h3>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="about" value="About Yourself" />
                                <textarea id="about" name="about" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('about', $profile->about) }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="expectations" value="Looking For" />
                                <textarea id="expectations" name="expectations" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('expectations', $profile->expectations) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Verification</h3>
                        <p class="text-sm text-gray-500 mb-3">Leave file fields blank to keep your current uploads. Changing your CNIC photo will trigger re-verification.</p>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Verification</h3>

                            @if ($profile->cnic_number && $profile->cnic_front_image && $profile->cnic_back_image)
                            <div class="bg-gray-50 border border-gray-200 rounded p-4 text-sm text-gray-600">
                                <p class="font-medium text-gray-700 mb-1">🔒 CNIC Verification Submitted</p>
                                <p>CNIC Number: {{ $profile->cnic_number }}</p>
                                <p class="mt-1">Your CNIC details are locked and cannot be changed here. If you need to update your CNIC, please contact support.</p>
                            </div>
                            @else
                            <p class="text-sm text-gray-500 mb-3">Your CNIC will only be used for verification and is never shown publicly.</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @if (!$profile->cnic_number)
                                <div>
                                    <x-input-label for="cnic_number" value="CNIC Number" />
                                    <x-text-input id="cnic_number" name="cnic_number" type="text" class="w-full mt-1" :value="old('cnic_number')" required/>
                                </div>
                                @endif
                                @if (!$profile->cnic_front_image)
                                <div>
                                    <x-input-label for="cnic_front_image" value="CNIC Photo (Front)" />
                                    <input id="cnic_front_image" name="cnic_front_image" type="file" accept="image/*" class="w-full mt-1" required>
                                </div>
                                @endif
                                @if (!$profile->cnic_back_image)
                                <div>
                                    <x-input-label for="cnic_back_image" value="CNIC Photo (Back)" />
                                    <input id="cnic_back_image" name="cnic_back_image" type="file" accept="image/*" class="w-full mt-1" required>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>                       
                        <hr class="mt-4 border-b">
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="allow_photo_sharing" name="allow_photo_sharing" value="1" checked class="rounded">
                            <x-input-label for="allow_photo_sharing" value="Allow my photo to be shared with a match after mutual interest is accepted" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="photo" value="Your Photo (replace)" />
                            <input id="photo" name="photo" type="file" accept="image/*" class="w-full mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="visibility" value="Profile Visibility" />
                        <select id="visibility" name="visibility" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                            <option value="public" {{ old('visibility', $profile->visibility) === 'public' ? 'selected' : '' }}>Public</option>
                            <option value="private" {{ old('visibility', $profile->visibility) === 'private' ? 'selected' : '' }}>Private</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('nikah.show') }}" class="px-4 py-2 text-sm text-gray-600">Cancel</a>
                        <x-primary-button>Update Profile</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>