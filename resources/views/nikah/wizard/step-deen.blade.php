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

                <form method="POST" action="{{ route('nikah.create.step.save', 'deen') }}" class="space-y-6">
                    @csrf

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">{{ __('db.Deen & Lifestyle') }}</h3>
                        <p class="text-sm text-gray-500 mb-3">{{ __('db.These help us match you with someone at a similar stage of practice — all optional, but the more you share, the better your matches.') }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div x-data="{ sect: '{{ old('sect', $data['sect'] ?? '') }}' }">
                                <x-input-label for="sect" :value="__('db.Sect')" />
                                <select id="sect" name="sect" x-model="sect" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">{{ __('db.Select Sect') }}</option>
                                    <option value="Sunni">{{ __('db.Sunni') }}</option>
                                    <option value="Shia">{{ __('db.Shia') }}</option>
                                    <option value="Ahle Hadith">{{ __('db.Ahle Hadith') }}</option>
                                    <option value="Deobandi">{{ __('db.Deobandi') }}</option>
                                    <option value="Other">{{ __('db.Other') }}</option>
                                </select>
                                <div x-show="sect === 'Other'" x-cloak class="mt-2">
                                    <x-text-input name="sect_other" type="text" class="w-full" placeholder="{{ __('db.Please specify your sect') }}" :value="old('sect_other', $data['sect_other'] ?? '')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="prayer_frequency" :value="__('db.Prayer (Salah) Regularity')" />
                                @php $pf = old('prayer_frequency', $data['prayer_frequency'] ?? ''); @endphp
                                <select id="prayer_frequency" name="prayer_frequency" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">{{ __('db.Prefer not to say') }}</option>
                                    <option value="always" {{ $pf === 'always' ? 'selected' : '' }}>{{ __('db.Always — 5 times a day') }}</option>
                                    <option value="usually" {{ $pf === 'usually' ? 'selected' : '' }}>{{ __('db.Usually') }}</option>
                                    <option value="sometimes" {{ $pf === 'sometimes' ? 'selected' : '' }}>{{ __('db.Sometimes') }}</option>
                                    <option value="rarely" {{ $pf === 'rarely' ? 'selected' : '' }}>{{ __('db.Rarely') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="hijab_or_beard" :value="__('db.Hijab (for sisters) / Beard (for brothers)')" />
                                @php $hb = old('hijab_or_beard', $data['hijab_or_beard'] ?? ''); @endphp
                                <select id="hijab_or_beard" name="hijab_or_beard" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">{{ __('db.Prefer not to say') }}</option>
                                    <option value="yes" {{ $hb === 'yes' ? 'selected' : '' }}>{{ __('db.Yes') }}</option>
                                    <option value="sometimes" {{ $hb === 'sometimes' ? 'selected' : '' }}>{{ __('db.Sometimes') }}</option>
                                    <option value="no" {{ $hb === 'no' ? 'selected' : '' }}>{{ __('db.No') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="smokes" :value="__('db.Smoking')" />
                                @php $sm = old('smokes', $data['smokes'] ?? ''); @endphp
                                <select id="smokes" name="smokes" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">{{ __('db.Prefer not to say') }}</option>
                                    <option value="no" {{ $sm === 'no' ? 'selected' : '' }}>{{ __('db.No') }}</option>
                                    <option value="occasionally" {{ $sm === 'occasionally' ? 'selected' : '' }}>{{ __('db.Occasionally') }}</option>
                                    <option value="yes" {{ $sm === 'yes' ? 'selected' : '' }}>{{ __('db.Yes') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="diet" :value="__('db.Diet')" />
                                @php $di = old('diet', $data['diet'] ?? ''); @endphp
                                <select id="diet" name="diet" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">{{ __('db.Prefer not to say') }}</option>
                                    <option value="halal_only" {{ $di === 'halal_only' ? 'selected' : '' }}>{{ __('db.Halal only') }}</option>
                                    <option value="halal_mostly" {{ $di === 'halal_mostly' ? 'selected' : '' }}>{{ __('db.Halal, mostly') }}</option>
                                    <option value="no_restriction" {{ $di === 'no_restriction' ? 'selected' : '' }}>{{ __('db.No restriction') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="open_to_polygamy" name="open_to_polygamy" value="1" {{ old('open_to_polygamy', $data['open_to_polygamy'] ?? false) ? 'checked' : '' }} class="rounded">
                            <x-input-label for="open_to_polygamy" :value="__('db.I am open to a polygamous marriage (e.g. as/marrying a second wife)')" />
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('nikah.create.step', 'family') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button>{{ __('db.Next') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
