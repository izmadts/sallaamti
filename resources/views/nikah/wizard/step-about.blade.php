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

                <form method="POST" action="{{ route('nikah.create.step.save', 'about') }}" class="space-y-6">
                    @csrf

                    <x-nikah-section :title="__('db.About')" icon="💬" color="purple">
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="about" :value="__('db.About Yourself')" />
                                <textarea id="about" name="about" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    placeholder="{{ __('db.Tell us about your personality, values, daily routine, and what makes you, you...') }}"
                                    title="{{ __('db.This is what other members read first — a few honest sentences go a long way.') }}">{{ old('about', $data['about'] ?? '') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="expectations" :value="__('db.What are you looking for in a partner?')" />
                                <textarea id="expectations" name="expectations" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    placeholder="{{ __('db.e.g. Someone practicing, family-oriented, kind-hearted...') }}"
                                    title="{{ __('db.What qualities matter most to you in a spouse.') }}">{{ old('expectations', $data['expectations'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section :title="__('db.Partner Preferences (optional)')" icon="🎯" color="sky" :description="__('db.Tell us what you\'re looking for so we can surface better matches — none of this is required.')">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="pref_min_age" :value="__('db.Minimum Age')" />
                                <x-text-input id="pref_min_age" name="pref_min_age" type="number" class="w-full mt-1" :value="old('pref_min_age', $data['pref_min_age'] ?? '')"
                                    placeholder="{{ __('db.e.g. 22') }}" title="{{ __('db.Leave blank for no minimum.') }}" />
                            </div>
                            <div>
                                <x-input-label for="pref_max_age" :value="__('db.Maximum Age')" />
                                <x-text-input id="pref_max_age" name="pref_max_age" type="number" class="w-full mt-1" :value="old('pref_max_age', $data['pref_max_age'] ?? '')"
                                    placeholder="{{ __('db.e.g. 30') }}" title="{{ __('db.Leave blank for no maximum.') }}" />
                            </div>
                            <div>
                                <x-input-label for="pref_city" :value="__('db.Preferred City')" />
                                <x-text-input id="pref_city" name="pref_city" type="text" class="w-full mt-1" :value="old('pref_city', $data['pref_city'] ?? '')"
                                    placeholder="{{ __('db.e.g. Karachi (leave blank for any city)') }}" title="{{ __('db.Leave blank if the city doesn\'t matter to you.') }}" />
                            </div>
                            <div>
                                <x-input-label for="pref_sect" :value="__('db.Preferred Sect')" />
                                <x-text-input id="pref_sect" name="pref_sect" type="text" class="w-full mt-1" :value="old('pref_sect', $data['pref_sect'] ?? '')"
                                    placeholder="{{ __('db.e.g. Sunni (leave blank for any)') }}" title="{{ __('db.Leave blank if sect doesn\'t matter to you.') }}" />
                            </div>
                            <div>
                                <x-input-label for="pref_education" :value="__('db.Preferred Education')" />
                                <x-text-input id="pref_education" name="pref_education" type="text" class="w-full mt-1" :value="old('pref_education', $data['pref_education'] ?? '')"
                                    placeholder="{{ __('db.e.g. Graduate or above (leave blank for any)') }}" title="{{ __('db.Leave blank if education level doesn\'t matter to you.') }}" />
                            </div>
                            <div>
                                <x-input-label for="pref_marital_status" :value="__('db.Preferred Marital Status')" />
                                <x-text-input id="pref_marital_status" name="pref_marital_status" type="text" class="w-full mt-1" :value="old('pref_marital_status', $data['pref_marital_status'] ?? '')"
                                    placeholder="{{ __('db.e.g. Never Married (leave blank for any)') }}" title="{{ __('db.Leave blank if this doesn\'t matter to you.') }}" />
                            </div>
                        </div>
                    </x-nikah-section>

                    <div class="flex justify-between">
                        <a href="{{ route('nikah.create.step', 'deen') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button>{{ __('db.Next: Photos & Verification') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
