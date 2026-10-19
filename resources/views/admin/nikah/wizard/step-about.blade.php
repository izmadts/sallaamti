<x-dynamic-component :component="$routePrefix === 'matchmaker' ? 'matchmaker-layout' : 'admin-layout'">
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route($routePrefix === 'matchmaker' ? 'matchmaker.nikah.index' : 'admin.nikah.verifications') }}" class="text-gray-400 hover:text-gray-600">Nikah Profiles</a>
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

                <form method="POST" action="{{ route($routePrefix . '.nikah.profiles.create.step.save', 'about') }}" class="space-y-6">
                    @csrf

                    <x-nikah-section title="About" icon="💬" color="purple">
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="about" value="About (optional)" />
                                <textarea id="about" name="about" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    placeholder="A few lines about their personality, values, daily routine...">{{ old('about', $data['about'] ?? '') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="expectations" value="Looking For (optional)" />
                                <textarea id="expectations" name="expectations" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1"
                                    placeholder="e.g. Someone practicing, family-oriented, kind-hearted..." title="Fill this in now if they've told you, or leave blank and add it later — the client can also add it themselves once logged in.">{{ old('expectations', $data['expectations'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section title="Partner Preferences (optional)" icon="🎯" color="sky">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="pref_min_age" value="Minimum Age (optional)" />
                                <x-text-input id="pref_min_age" name="pref_min_age" type="number" class="w-full mt-1" :value="old('pref_min_age', $data['pref_min_age'] ?? '')" placeholder="e.g. 22" />
                            </div>
                            <div>
                                <x-input-label for="pref_max_age" value="Maximum Age (optional)" />
                                <x-text-input id="pref_max_age" name="pref_max_age" type="number" class="w-full mt-1" :value="old('pref_max_age', $data['pref_max_age'] ?? '')" placeholder="e.g. 30" />
                            </div>
                            <div>
                                <x-input-label for="pref_city" value="Preferred City (optional)" />
                                <x-text-input id="pref_city" name="pref_city" type="text" class="w-full mt-1" :value="old('pref_city', $data['pref_city'] ?? '')" placeholder="e.g. Karachi (leave blank for any city)" />
                            </div>
                            <div>
                                <x-input-label for="pref_sect" value="Preferred Sect (optional)" />
                                <x-text-input id="pref_sect" name="pref_sect" type="text" class="w-full mt-1" :value="old('pref_sect', $data['pref_sect'] ?? '')" placeholder="e.g. Sunni (leave blank for any)" />
                            </div>
                            <div>
                                <x-input-label for="pref_education" value="Preferred Education (optional)" />
                                <x-text-input id="pref_education" name="pref_education" type="text" class="w-full mt-1" :value="old('pref_education', $data['pref_education'] ?? '')" placeholder="e.g. Graduate or above (leave blank for any)" />
                            </div>
                            @php $prefMs = old('pref_marital_status', $data['pref_marital_status'] ?? ''); @endphp
                            <div>
                                <x-input-label for="pref_marital_status" value="Preferred Marital Status (optional)" />
                                <select id="pref_marital_status" name="pref_marital_status" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="" {{ $prefMs === '' ? 'selected' : '' }}>Any</option>
                                    @foreach (['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'separated' => 'Separated', 'married' => 'Married'] as $val => $label)
                                    <option value="{{ $val }}" {{ $prefMs === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="pref_income" value="Preferred Income / Business Worth (optional)" />
                                <x-text-input id="pref_income" name="pref_income" type="text" class="w-full mt-1" :value="old('pref_income', $data['pref_income'] ?? '')" placeholder="e.g. PKR 150,000+ monthly (leave blank for any)" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="pref_other" value="Other Preferences (optional)" />
                                <textarea id="pref_other" name="pref_other" rows="2" class="border-gray-300 rounded-md shadow-sm w-full mt-1" placeholder="Anything else they're looking for in a partner">{{ old('pref_other', $data['pref_other'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </x-nikah-section>

                    <x-nikah-section title="Profile Visibility" icon="👁️" color="teal">
                        @php $vis = old('visibility', $data['visibility'] ?? 'public'); @endphp
                        <select id="visibility" name="visibility" required class="border-gray-300 rounded-md shadow-sm w-full">
                            <option value="public" {{ $vis === 'public' ? 'selected' : '' }}>Public (shows in browse + Google)</option>
                            <option value="members_only" {{ $vis === 'members_only' ? 'selected' : '' }}>Members Only (shows in browse, not Google)</option>
                            <option value="matchmaker_assisted" {{ $vis === 'matchmaker_assisted' ? 'selected' : '' }}>Matchmaker-Assisted Only (hidden from member browse, matchmakers can still find it)</option>
                            <option value="confidential" {{ $vis === 'confidential' ? 'selected' : '' }}>Confidential (hidden from all search/browse — ID-only access)</option>
                        </select>
                    </x-nikah-section>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
                        🔗 CNIC and photo aren't collected here — after payment, you'll land on the client's page where you can set their login password and share their secure self-upload link.
                    </div>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route($routePrefix . '.nikah.profiles.create.step', 'deen') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← Back</a>
                        <x-primary-button>Next: Payment →</x-primary-button>
                    </div>
                </form>

                @if (auth()->user()->hasRole('admin'))
                <form method="POST" action="{{ route($routePrefix . '.nikah.profiles.create.step.skip', 'about') }}" class="text-right mt-2">
                    @csrf
                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 underline">Skip (admin preview — no data saved)</button>
                </form>
                @endif

            </div>
        </div>
    </div>
</x-dynamic-component>
