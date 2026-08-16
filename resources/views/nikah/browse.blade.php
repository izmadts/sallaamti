<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Browse Matches</h2>
    </x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Browse Matches</h2>
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
                <div>
                    <label class="text-xs text-gray-500">Education</label>
                    <input type="text" name="education" value="{{ request('education') }}" list="education-filter-list" autocomplete="off" placeholder="Type to search" class="border-gray-300 rounded text-sm block w-32">
                    <datalist id="education-filter-list">
                        @foreach (['Matric / O-Levels', 'Intermediate / A-Levels', "Bachelor's", "Master's", 'MPhil / MS', 'PhD', 'Madrassah / Islamic Education'] as $lvl)
                        <option value="{{ $lvl }}">
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Ethnicity</label>
                    <input type="text" name="ethnicity" value="{{ request('ethnicity') }}" class="border-gray-300 rounded text-sm block w-24">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Language</label>
                    <input type="text" name="language" value="{{ request('language') }}" list="language-filter-list" autocomplete="off" placeholder="Type to search" class="border-gray-300 rounded text-sm block w-28">
                    <datalist id="language-filter-list">
                        @foreach (['Urdu', 'English', 'Punjabi', 'Pashto', 'Sindhi', 'Saraiki', 'Balochi'] as $lang)
                        <option value="{{ $lang }}">
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Family Type</label>
                    <input type="text" name="family_type" value="{{ request('family_type') }}" list="family-type-filter-list" autocomplete="off" placeholder="Type to search" class="border-gray-300 rounded text-sm block w-32">
                    <datalist id="family-type-filter-list">
                        @foreach (['Joint Family', 'Nuclear Family', 'Living with In-Laws'] as $ft)
                        <option value="{{ $ft }}">
                        @endforeach
                    </datalist>
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
                <div>
                    <label class="text-xs text-gray-500">Min Height</label>
                    <select name="min_height" class="border-gray-300 rounded text-sm block">
                        <option value="">Any</option>
                        @foreach ($heightFilterOptions as $h)
                        <option value="{{ $h }}" {{ request('min_height') === $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Max Height</label>
                    <select name="max_height" class="border-gray-300 rounded text-sm block">
                        <option value="">Any</option>
                        @foreach ($heightFilterOptions as $h)
                        <option value="{{ $h }}" {{ request('max_height') === $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
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
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="mt-1 me-2 text-right">
                        <details class="relative inline-block text-left">
                            <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600 list-none">⋯ More</summary>
                            <div class="absolute right-0 mt-1 bg-white border rounded shadow-lg z-10 w-32">
                                <form method="POST" action="{{ route('nikah.block', $profile) }}">
                                    @csrf
                                    <button class="w-full text-left text-xs px-3 py-2 hover:bg-gray-50 text-gray-600">🚫 Block</button>
                                </form>
                                <button onclick="document.getElementById('report-{{ $profile->id }}').classList.toggle('hidden')" class="w-full text-left text-xs px-3 py-2 hover:bg-gray-50 text-red-500">⚑ Report</button>
                            </div>
                        </details>
                        <div id="report-{{ $profile->id }}" class="hidden mt-2">
                            <form method="POST" action="{{ route('nikah.report', $profile) }}" class="text-xs space-y-1">
                                @csrf
                                <input type="text" name="reason" placeholder="Reason" class="border rounded w-full px-2 py-1 text-xs" required>
                                <button class="bg-red-600 text-white px-2 py-1 rounded text-xs w-full">Submit Report</button>
                            </form>
                        </div>
                    </div>

                    {{-- Photo --}}
                    <div class="relative h-48 bg-gray-100 flex items-center justify-center">
                        {{-- Trust badge stack — independently-earned signals instead of one binary "Verified" --}}
                        <div class="absolute top-2 left-1 flex flex-col gap-1 items-start">
                            @php $badges = $profile->trustBadges(); @endphp
                            @if ($badges['cnic'])
                            <span class="text-xs bg-green-500 text-white px-2 py-0.5 rounded-full shadow" title="CNIC verified by our team">🪪 CNIC Verified</span>
                            @endif
                            @if ($badges['payment'])
                            <span class="text-xs bg-blue-500 text-white px-2 py-0.5 rounded-full shadow" title="Verification fee payment confirmed">💳 Payment Verified</span>
                            @endif
                            @if ($badges['guardian'])
                            <span class="text-xs bg-purple-500 text-white px-2 py-0.5 rounded-full shadow" title="Guardian contact confirmed by our team">👨‍👩‍👦 Guardian Verified</span>
                            @endif
                        </div>
                        @if ($profile->photos->first())
                        <img src="{{ route('nikah.photos.show', $profile->photos->first()) }}" alt="Profile photo (blurred until you connect)" class="w-full h-full object-cover blur-md">
                        <div class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm">🔒 Photo hidden</div>
                        @else
                        <div class="text-5xl">👤</div>
                        @endif

                        {{-- Match % badge --}}
                        @if ($profile->match_percentage > 0)
                        <div class="absolute top-2 right-2 bg-white rounded-full px-2 py-0.5 text-xs font-bold
                                    {{ $profile->match_percentage >= 80 ? 'text-green-600' :
                                       ($profile->match_percentage >= 50 ? 'text-yellow-600' : 'text-gray-500') }}">
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
                                            {{ $profile->match_percentage >= 80 ? 'bg-green-500' :
                                               ($profile->match_percentage >= 50 ? 'bg-yellow-500' : 'bg-gray-400') }}"
                                    style="width: {{ $profile->match_percentage }}%"></div>
                            </div>
                            @if (!empty($profile->match_criteria))
                            <details class="mt-1">
                                <summary class="text-xs text-teal-700 cursor-pointer">Why {{ $profile->match_percentage }}%?</summary>
                                <ul class="mt-1 space-y-0.5">
                                    @foreach ($profile->match_criteria as $c)
                                    <li class="text-xs {{ $c['matched'] ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ $c['matched'] ? '✓' : '✗' }} {{ $c['label'] }}
                                    </li>
                                    @endforeach
                                </ul>
                            </details>
                            @endif
                        </div>
                        @endif

                        {{-- Add this link wrapper around the profile info section --}}
                        <a href="{{ route('nikah.profile.view', $profile) }}" class="block hover:opacity-90">
                            <h4 class="font-semibold text-gray-800 text-base">{{ $profile->age }} yrs · {{ $profile->city }}</h4>
                            <div class="mt-1 space-y-0.5">
                                @if ($profile->profession)<p class="text-xs text-gray-500">💼 {{ $profile->profession }}</p>@endif
                                @if ($profile->education)<p class="text-xs text-gray-500">🎓 {{ $profile->education }}</p>@endif
                                @if ($profile->sect)<p class="text-xs text-gray-500">☪️ {{ $profile->sect }}</p>@endif
                                @if ($profile->ethnicity)<p class="text-xs text-gray-500">🌍 {{ $profile->ethnicity }}</p>@endif
                                <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $profile->marital_status)) }}</p>
                                <p class="text-xs text-gray-400">
                                    Member since {{ $profile->created_at->format('M Y') }}
                                    @if ($profile->last_active_at && $profile->last_active_at->gt(now()->subDays(7)))
                                    · <span class="text-green-600">Active {{ $profile->last_active_at->diffForHumans() }}</span>
                                    @endif
                                </p>
                            </div>
                        </a>


                        <div class="mt-3 flex gap-2">
                            {{-- Express Interest --}}
                            @if (in_array($profile->id, $sentInterestIds))
                            <button disabled class="flex-1 bg-gray-200 text-gray-500 text-sm py-2 rounded">Sent ✓</button>
                            @else
                            <form method="POST" action="{{ route('nikah.interest.send', $profile) }}" class="flex-1">
                                @csrf
                                <button class="w-full bg-pink-600 text-white text-sm py-2 rounded hover:bg-pink-700">Express Interest</button>
                            </form>
                            @endif

                            {{-- Save/Bookmark --}}
                            <form method="POST" action="{{ route('nikah.save', $profile) }}">
                                @csrf
                                <button class="px-3 py-2 rounded border {{ in_array($profile->id, $savedProfileIds) ? 'bg-yellow-100 border-yellow-300 text-yellow-600' : 'border-gray-200 text-gray-400' }}">
                                    {{ in_array($profile->id, $savedProfileIds) ? '★' : '☆' }}
                                </button>
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