<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('db.Check Profiles') }}</h2>
    </x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('db.Check Profiles') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('nikah.saved') }}" class="text-sm bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50">
                    ★ Saved Profiles
                </a>
                <a href="{{ route('nikah.interests') }}" class="text-sm bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50">
                    💌 My Interests
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">


            {{-- Preferences reminder --}}
            @if (!$myProfile->pref_min_age && !$myProfile->pref_city && !$myProfile->pref_sect)
            <div class="p-4 bg-blue-50 border border-blue-100 text-blue-700 rounded-lg text-sm flex justify-between items-center">
                <span>💡 Set your match preferences to see accurate match percentages.</span>
                <a href="{{ route('nikah.edit') }}" class="underline font-medium ml-2 whitespace-nowrap">Set Preferences →</a>
            </div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs text-gray-500">City</label>
                    <input type="text" name="city" value="{{ request('city') }}" class="border-gray-300 rounded text-sm block w-32">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Min Age</label>
                    <input type="number" name="min_age" value="{{ request('min_age') }}" class="border-gray-300 rounded text-sm block w-16">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Max Age</label>
                    <input type="number" name="max_age" value="{{ request('max_age') }}" class="border-gray-300 rounded text-sm block w-16">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Sect</label>
                    <input type="text" name="sect" value="{{ request('sect') }}" class="border-gray-300 rounded text-sm block w-24">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Marital Status</label>
                    <select name="marital_status" class="border-gray-300 rounded text-sm block">
                        <option value="">Any</option>
                        <option value="never_married" {{ request('marital_status') === 'never_married' ? 'selected' : '' }}>Never Married</option>
                        <option value="divorced" {{ request('marital_status') === 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="widowed" {{ request('marital_status') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="separated" {{ request('marital_status') === 'separated' ? 'selected' : '' }}>Separated</option>
                        <option value="married" {{ request('marital_status') === 'married' ? 'selected' : '' }}>Married (Second Wife)</option>
                    </select>
                </div>
                @php $educationFilterOptions = ['Matric / O-Levels', 'Intermediate / A-Levels', "Bachelor's", "Master's", 'MPhil / MS', 'PhD', 'Madrassah / Islamic Education']; @endphp
                <div class="w-32">
                    <label class="text-xs text-gray-500">Education</label>
                    <x-searchable-select name="education" :options="$educationFilterOptions" :value="request('education')" class="text-sm" placeholder="Any" />
                </div>
                <div>
                    <label class="text-xs text-gray-500">Ethnicity</label>
                    <input type="text" name="ethnicity" value="{{ request('ethnicity') }}" class="border-gray-300 rounded text-sm block w-24">
                </div>
                @php $languageFilterOptions = ['Urdu', 'English', 'Punjabi', 'Pashto', 'Sindhi', 'Saraiki', 'Balochi']; @endphp
                <div class="w-28">
                    <label class="text-xs text-gray-500">Language</label>
                    <x-searchable-select name="language" :options="$languageFilterOptions" :value="request('language')" class="text-sm" placeholder="Any" />
                </div>
                @php $familyTypeFilterOptions = ['Joint Family', 'Nuclear Family', 'Living with In-Laws']; @endphp
                <div class="w-32">
                    <label class="text-xs text-gray-500">Family Type</label>
                    <x-searchable-select name="family_type" :options="$familyTypeFilterOptions" :value="request('family_type')" class="text-sm" placeholder="Any" />
                </div>
                @php
                    $heightFilterOptions = [];
                    for ($ft = 4; $ft <= 7; $ft++) {
                        for ($in = 0; $in <= 11; $in++) {
                            if ($ft === 7 && $in > 0) break;
                            $heightFilterOptions[] = $ft . "'" . $in . '"';
                        }
                    }
                @endphp
                <div class="w-24">
                    <label class="text-xs text-gray-500">Min Height</label>
                    <x-searchable-select name="min_height" :options="$heightFilterOptions" :value="request('min_height')" class="text-sm" placeholder="Any" />
                </div>
                <div class="w-24">
                    <label class="text-xs text-gray-500">Max Height</label>
                    <x-searchable-select name="max_height" :options="$heightFilterOptions" :value="request('max_height')" class="text-sm" placeholder="Any" />
                </div>
                <div>
                    <label class="text-xs text-gray-500">Prayer Regularity</label>
                    <select name="prayer_frequency" class="border-gray-300 rounded text-sm block">
                        <option value="">Any</option>
                        <option value="always" {{ request('prayer_frequency') === 'always' ? 'selected' : '' }}>Always</option>
                        <option value="usually" {{ request('prayer_frequency') === 'usually' ? 'selected' : '' }}>Usually</option>
                        <option value="sometimes" {{ request('prayer_frequency') === 'sometimes' ? 'selected' : '' }}>Sometimes</option>
                        <option value="rarely" {{ request('prayer_frequency') === 'rarely' ? 'selected' : '' }}>Rarely</option>
                    </select>
                </div>
                <div class="flex items-center gap-1.5 pb-2">
                    <input type="checkbox" id="open_to_polygamy" name="open_to_polygamy" value="1" {{ request()->boolean('open_to_polygamy') ? 'checked' : '' }} class="rounded">
                    <label for="open_to_polygamy" class="text-xs text-gray-500">Open to polygamy</label>
                </div>
                <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded">🔍 Search</button>
                <a href="{{ route('nikah.browse') }}" class="text-sm text-gray-400">Reset</a>
            </form>

            {{-- Results --}}
            <p class="text-sm text-gray-500">{{ $paginated->total() }} profiles found — sorted by match %</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($paginated as $profile)
                @php $badges = $profile->trustBadges(); @endphp
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition duration-300 hover:shadow-xl hover:-translate-y-1">

                    {{-- Photo --}}
                    <div class="relative h-52 bg-gradient-to-br from-teal-50 to-gray-100 flex items-center justify-center overflow-hidden">
                        @if ($profile->photos->first())
                        <img src="{{ route('nikah.photos.show', $profile->photos->first()) }}" alt="Profile photo (blurred until you connect)" class="w-full h-full object-cover blur-md scale-105 group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 flex items-center justify-center text-white text-sm font-medium bg-black/10">
                            <span class="bg-black/40 backdrop-blur-sm px-3 py-1 rounded-full">🔒 Photo hidden</span>
                        </div>
                        @else
                        <div class="text-6xl opacity-30">👤</div>
                        @endif

                        {{-- Soft gradient at the bottom so badges/text always stay legible over any photo --}}
                        <div class="absolute inset-x-0 bottom-0 h-14 bg-gradient-to-t from-black/40 to-transparent"></div>

                        {{-- "More" menu --}}
                        <details class="absolute top-2 end-2 z-10">
                            <summary class="w-7 h-7 flex items-center justify-center rounded-full bg-white/90 backdrop-blur text-gray-500 cursor-pointer hover:bg-white shadow list-none text-sm">⋯</summary>
                            <div class="absolute end-0 mt-1 bg-white border rounded-lg shadow-lg z-10 w-36 overflow-hidden">
                                <form method="POST" action="{{ route('nikah.block', $profile) }}">
                                    @csrf
                                    <button class="w-full text-left text-xs px-3 py-2 hover:bg-gray-50 text-gray-600">🚫 Block</button>
                                </form>
                                <button onclick="document.getElementById('report-{{ $profile->id }}').classList.toggle('hidden')" class="w-full text-left text-xs px-3 py-2 hover:bg-gray-50 text-red-500">⚑ Report</button>
                            </div>
                        </details>

                        {{-- Trust badge stack — independently-earned signals instead of one binary "Verified" --}}
                        <div class="absolute top-2 start-2 flex flex-col gap-1 items-start">
                            @if ($badges['cnic'])
                            <span class="text-[11px] font-semibold bg-emerald-500 text-white px-2 py-0.5 rounded-full shadow flex items-center gap-1" title="CNIC verified by our team">🪪 CNIC Verified</span>
                            @endif
                            @if ($badges['payment'])
                            <span class="text-[11px] font-semibold bg-blue-500 text-white px-2 py-0.5 rounded-full shadow flex items-center gap-1" title="Verification fee payment confirmed">💳 Payment Verified</span>
                            @endif
                            @if ($badges['guardian'])
                            <span class="text-[11px] font-semibold bg-purple-500 text-white px-2 py-0.5 rounded-full shadow flex items-center gap-1" title="Guardian contact confirmed by our team">👨‍👩‍👦 Guardian Verified</span>
                            @endif
                            @if (!$badges['cnic'] && !$badges['payment'] && !$badges['guardian'])
                            <span class="text-[11px] font-semibold bg-gray-500/90 text-white px-2 py-0.5 rounded-full shadow flex items-center gap-1" title="Our team hasn't finished reviewing this profile yet">⏳ Verification Pending</span>
                            @endif
                        </div>

                        {{-- New badge --}}
                        @if ($profile->created_at->gt(now()->subDays(14)))
                        <span class="absolute bottom-2 start-2 text-[11px] font-bold text-white px-2 py-0.5 rounded-full shadow" style="background: var(--gold)">✨ New</span>
                        @endif

                        {{-- Match % badge --}}
                        @if ($profile->match_percentage > 0)
                        <div class="absolute bottom-2 end-2 rounded-full px-2.5 py-1 text-xs font-bold shadow text-white
                                    {{ $profile->match_percentage >= 80 ? 'bg-emerald-500' :
                                       ($profile->match_percentage >= 50 ? 'bg-amber-500' : 'bg-gray-500') }}">
                            {{ $profile->match_percentage }}% match
                        </div>
                        @endif
                    </div>

                    <div class="p-4">
                        {{-- Match bar --}}
                        @if ($profile->match_percentage > 0)
                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Match Score</span>
                                <span>{{ $profile->match_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full
                                            {{ $profile->match_percentage >= 80 ? 'bg-emerald-500' :
                                               ($profile->match_percentage >= 50 ? 'bg-amber-500' : 'bg-gray-400') }}"
                                    style="width: {{ $profile->match_percentage }}%"></div>
                            </div>
                            @if (!empty($profile->match_criteria))
                            <details class="mt-1">
                                <summary class="text-xs font-medium cursor-pointer" style="color: var(--teal)">Why {{ $profile->match_percentage }}%?</summary>
                                <ul class="mt-1 space-y-0.5">
                                    @foreach ($profile->match_criteria as $c)
                                    <li class="text-xs {{ $c['matched'] ? 'text-emerald-600' : 'text-gray-400' }}">
                                        {{ $c['matched'] ? '✓' : '✗' }} {{ $c['label'] }}
                                    </li>
                                    @endforeach
                                </ul>
                            </details>
                            @endif
                        </div>
                        @endif

                        <a href="{{ route('nikah.profile.view', $profile) }}" class="block">
                            <h4 class="font-bold text-gray-800 text-lg group-hover:text-[--teal] transition">{{ $profile->age }} yrs · {{ $profile->city }}</h4>
                            <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1">
                                @if ($profile->profession)<span class="text-xs text-gray-600 flex items-center gap-1">💼 {{ $profile->profession }}</span>@endif
                                @if ($profile->education)<span class="text-xs text-gray-600 flex items-center gap-1">🎓 {{ $profile->education }}</span>@endif
                                @if ($profile->sect)<span class="text-xs text-gray-600 flex items-center gap-1">☪️ {{ $profile->sect }}</span>@endif
                                @if ($profile->ethnicity)<span class="text-xs text-gray-600 flex items-center gap-1">🌍 {{ $profile->ethnicity }}</span>@endif
                            </div>
                            <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">{{ ucfirst(str_replace('_', ' ', $profile->marital_status)) }}</span>
                                <span class="text-xs text-gray-400">
                                    @if ($profile->last_active_at && $profile->last_active_at->gt(now()->subDays(7)))
                                    <span class="text-emerald-600 font-medium">● Active {{ $profile->last_active_at->diffForHumans(null, true) }} ago</span>
                                    @else
                                    Member since {{ $profile->created_at->format('M Y') }}
                                    @endif
                                </span>
                            </div>
                        </a>

                        <div class="mt-3 flex gap-2">
                            {{-- Express Interest --}}
                            @if (in_array($profile->id, $sentInterestIds))
                            <button disabled class="flex-1 bg-gray-100 text-gray-400 text-sm font-medium py-2 rounded-lg">Sent ✓</button>
                            @else
                            <form method="POST" action="{{ route('nikah.interest.send', $profile) }}" class="flex-1">
                                @csrf
                                <button class="w-full text-white text-sm font-semibold py-2 rounded-lg shadow-sm transition hover:shadow-md hover:brightness-110" style="background: linear-gradient(135deg, #e11d78, #be185d)">💌 Express Interest</button>
                            </form>
                            @endif

                            {{-- Save/Bookmark --}}
                            <form method="POST" action="{{ route('nikah.save', $profile) }}">
                                @csrf
                                <button class="px-3 py-2 rounded-lg border transition {{ in_array($profile->id, $savedProfileIds) ? 'bg-amber-50 border-amber-300 text-amber-500' : 'border-gray-200 text-gray-300 hover:border-gray-300 hover:text-gray-400' }}">
                                    {{ in_array($profile->id, $savedProfileIds) ? '★' : '☆' }}
                                </button>
                            </form>
                        </div>

                        <div id="report-{{ $profile->id }}" class="hidden mt-2">
                            <form method="POST" action="{{ route('nikah.report', $profile) }}" class="text-xs space-y-1">
                                @csrf
                                <input type="text" name="reason" placeholder="Reason" class="border rounded w-full px-2 py-1 text-xs" required>
                                <button class="bg-red-600 text-white px-2 py-1 rounded text-xs w-full">Submit Report</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 col-span-full">No profiles found. Try adjusting your filters.</p>
                @endforelse
            </div>

            {{ $paginated->links() }}
        </div>
    </div>
</x-app-layout>