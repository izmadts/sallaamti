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

                <form method="POST" action="{{ route('admin.nikah.profiles.create.step.save', 'deen') }}" class="space-y-6">
                    @csrf

                    <x-nikah-section title="Deen & Lifestyle" icon="🕌" color="emerald">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div x-data="{ sect: '{{ old('sect', $data['sect'] ?? '') }}' }">
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
                                    <x-text-input name="sect_other" type="text" class="w-full" placeholder="Please specify" :value="old('sect_other', $data['sect_other'] ?? '')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="prayer_frequency" value="Prayer (Salah) Regularity" />
                                @php $pf = old('prayer_frequency', $data['prayer_frequency'] ?? ''); @endphp
                                <select id="prayer_frequency" name="prayer_frequency" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['always' => 'Always — 5 times a day', 'usually' => 'Usually', 'sometimes' => 'Sometimes', 'rarely' => 'Rarely'] as $val => $label)
                                    <option value="{{ $val }}" {{ $pf === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="hijab_or_beard" value="Hijab / Beard" />
                                @php $hb = old('hijab_or_beard', $data['hijab_or_beard'] ?? ''); @endphp
                                <select id="hijab_or_beard" name="hijab_or_beard" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['yes' => 'Yes', 'sometimes' => 'Sometimes', 'no' => 'No'] as $val => $label)
                                    <option value="{{ $val }}" {{ $hb === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="smokes" value="Smoking" />
                                @php $sm = old('smokes', $data['smokes'] ?? ''); @endphp
                                <select id="smokes" name="smokes" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['no' => 'No', 'occasionally' => 'Occasionally', 'yes' => 'Yes'] as $val => $label)
                                    <option value="{{ $val }}" {{ $sm === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="diet" value="Diet" />
                                @php $di = old('diet', $data['diet'] ?? ''); @endphp
                                <select id="diet" name="diet" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['halal_only' => 'Halal only', 'halal_mostly' => 'Halal, mostly', 'no_restriction' => 'No restriction'] as $val => $label)
                                    <option value="{{ $val }}" {{ $di === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="open_to_polygamy" name="open_to_polygamy" value="1" {{ old('open_to_polygamy', $data['open_to_polygamy'] ?? false) ? 'checked' : '' }} class="rounded">
                            <x-input-label for="open_to_polygamy" value="Open to a polygamous marriage" />
                        </div>
                    </x-nikah-section>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route('admin.nikah.profiles.create.step', 'family') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← Back</a>
                        <x-primary-button>Next: About →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-admin-layout>
