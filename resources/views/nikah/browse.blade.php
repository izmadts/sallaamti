<x-app-layout>
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

    {{-- Hero strip --}}
    <div class="text-white" style="background: linear-gradient(120deg, var(--teal-dark, #0d4f4f), var(--teal, #0d6b6b) 60%, #b8962e)">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center gap-4">
            <div class="text-4xl">💍</div>
            <div>
                <h3 class="text-lg font-bold">{{ __('db.Find your match, the halal way') }}</h3>
                <p class="text-teal-50 text-sm opacity-90">{{ __('db.Every profile below is a real member, guardian-verified where noted — sorted by how well they match your preferences.') }}</p>
            </div>
        </div>
    </div>

    <div class="py-6" style="background: #fbfaf7; min-height: 60vh">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Preferences reminder --}}
            @if (!$myProfile->pref_min_age && !$myProfile->pref_city && !$myProfile->pref_sect)
            <div class="p-4 bg-blue-50 border border-blue-100 text-blue-700 rounded-xl text-sm flex justify-between items-center">
                <span>💡 {{ __('db.Set your match preferences to see accurate match percentages.') }}</span>
                <a href="{{ route('nikah.edit') }}" class="underline font-medium ml-2 whitespace-nowrap">{{ __('db.Set Preferences') }} →</a>
            </div>
            @endif

            {{-- Filters --}}
            <details open class="bg-white rounded-2xl shadow-sm border-t-4 overflow-hidden" style="border-color: var(--teal)">
                <summary class="px-5 py-3 cursor-pointer font-semibold text-gray-700 flex items-center gap-2 list-none">
                    🔍 {{ __('db.Refine Your Search') }}
                    <span class="ms-auto text-xs font-normal text-gray-400">{{ __('db.tap to collapse') }}</span>
                </summary>
                <form method="GET" class="px-5 pb-5 flex flex-wrap gap-3 items-end border-t border-gray-100 pt-4">
                    <div>
                        <label class="text-xs text-gray-500">{{ __('db.City') }}</label>
                        <input type="text" name="city" value="{{ request('city') }}" class="border-gray-300 focus:border-[--teal] focus:ring-[--teal] rounded-lg text-sm block w-32">
                    </div>
                    <x-country-state-fields
                        country-name="country" state-name="state"
                        :country-value="request('country')" :state-value="request('state')"
                        :all-states="$countryStates"
                        label-class="text-xs text-gray-500"
                        input-class="border-gray-300 focus:border-[--teal] focus:ring-[--teal] rounded-lg text-sm block w-32" />
                    <div>
                        <label class="text-xs text-gray-500">{{ __('db.Min Age') }}</label>
                        <input type="number" name="min_age" value="{{ request('min_age') }}" class="border-gray-300 focus:border-[--teal] focus:ring-[--teal] rounded-lg text-sm block w-16">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">{{ __('db.Max Age') }}</label>
                        <input type="number" name="max_age" value="{{ request('max_age') }}" class="border-gray-300 focus:border-[--teal] focus:ring-[--teal] rounded-lg text-sm block w-16">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">{{ __('db.Sect') }}</label>
                        <input type="text" name="sect" value="{{ request('sect') }}" class="border-gray-300 focus:border-[--teal] focus:ring-[--teal] rounded-lg text-sm block w-24">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">{{ __('db.Marital Status') }}</label>
                        <select name="marital_status" class="border-gray-300 focus:border-[--teal] focus:ring-[--teal] rounded-lg text-sm block">
                            <option value="">{{ __('db.Any') }}</option>
                            <option value="never_married" {{ request('marital_status') === 'never_married' ? 'selected' : '' }}>{{ __('db.Never Married') }}</option>
                            <option value="divorced" {{ request('marital_status') === 'divorced' ? 'selected' : '' }}>{{ __('db.Divorced') }}</option>
                            <option value="widowed" {{ request('marital_status') === 'widowed' ? 'selected' : '' }}>{{ __('db.Widowed') }}</option>
                            <option value="separated" {{ request('marital_status') === 'separated' ? 'selected' : '' }}>{{ __('db.Separated') }}</option>
                            <option value="married" {{ request('marital_status') === 'married' ? 'selected' : '' }}>{{ __('db.Married (Second Wife)') }}</option>
                        </select>
                    </div>
                    @php $educationFilterOptions = ['Matric / O-Levels', 'Intermediate / A-Levels', "Bachelor's", "Master's", 'MPhil / MS', 'PhD', 'Madrassah / Islamic Education']; @endphp
                    <div class="w-32">
                        <label class="text-xs text-gray-500">{{ __('db.Education') }}</label>
                        <x-searchable-select name="education" :options="$educationFilterOptions" :value="request('education')" class="text-sm" placeholder="Any" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">{{ __('db.Ethnicity') }}</label>
                        <input type="text" name="ethnicity" value="{{ request('ethnicity') }}" class="border-gray-300 focus:border-[--teal] focus:ring-[--teal] rounded-lg text-sm block w-24">
                    </div>
                    @php $languageFilterOptions = ['Urdu', 'English', 'Punjabi', 'Pashto', 'Sindhi', 'Saraiki', 'Balochi']; @endphp
                    <div class="w-28">
                        <label class="text-xs text-gray-500">{{ __('db.Language') }}</label>
                        <x-searchable-select name="language" :options="$languageFilterOptions" :value="request('language')" class="text-sm" placeholder="Any" />
                    </div>
                    @php $familyTypeFilterOptions = ['Joint Family', 'Nuclear Family', 'Living with In-Laws']; @endphp
                    <div class="w-32">
                        <label class="text-xs text-gray-500">{{ __('db.Family Type') }}</label>
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
                        <label class="text-xs text-gray-500">{{ __('db.Min Height') }}</label>
                        <x-searchable-select name="min_height" :options="$heightFilterOptions" :value="request('min_height')" class="text-sm" placeholder="Any" />
                    </div>
                    <div class="w-24">
                        <label class="text-xs text-gray-500">{{ __('db.Max Height') }}</label>
                        <x-searchable-select name="max_height" :options="$heightFilterOptions" :value="request('max_height')" class="text-sm" placeholder="Any" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">{{ __('db.Prayer Regularity') }}</label>
                        <select name="prayer_frequency" class="border-gray-300 focus:border-[--teal] focus:ring-[--teal] rounded-lg text-sm block">
                            <option value="">{{ __('db.Any') }}</option>
                            <option value="always" {{ request('prayer_frequency') === 'always' ? 'selected' : '' }}>{{ __('db.Always') }}</option>
                            <option value="usually" {{ request('prayer_frequency') === 'usually' ? 'selected' : '' }}>{{ __('db.Usually') }}</option>
                            <option value="sometimes" {{ request('prayer_frequency') === 'sometimes' ? 'selected' : '' }}>{{ __('db.Sometimes') }}</option>
                            <option value="rarely" {{ request('prayer_frequency') === 'rarely' ? 'selected' : '' }}>{{ __('db.Rarely') }}</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1.5 pb-2">
                        <input type="checkbox" id="open_to_polygamy" name="open_to_polygamy" value="1" {{ request()->boolean('open_to_polygamy') ? 'checked' : '' }} class="rounded text-[--teal] focus:ring-[--teal]">
                        <label for="open_to_polygamy" class="text-xs text-gray-500">{{ __('db.Open to polygamy') }}</label>
                    </div>
                    <button class="text-white text-sm font-semibold px-5 py-2 rounded-lg shadow-sm hover:brightness-110 transition" style="background: var(--teal)">🔍 {{ __('db.Search') }}</button>
                    <a href="{{ route('nikah.browse') }}" class="text-sm text-gray-400 hover:text-gray-600">{{ __('db.Reset') }}</a>
                </form>
            </details>

            {{-- Results --}}
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-600">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-xs font-bold me-1" style="background: var(--teal)">{{ $paginated->total() }}</span>
                    {{ __('db.profiles found — sorted by match %') }}
                </p>
            </div>

            <div id="profile-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($paginated as $profile)
                @include('nikah.partials.profile-card', ['profile' => $profile, 'sentInterestIds' => $sentInterestIds, 'savedProfileIds' => $savedProfileIds])
                @empty
                <div class="col-span-full text-center py-16">
                    <div class="text-5xl mb-3 opacity-40">🔍</div>
                    <p class="text-gray-500">{{ __('db.No profiles found. Try adjusting your filters.') }}</p>
                </div>
                @endforelse
            </div>

            {{-- Infinite scroll status — auto-loads on scroll, but also offers an
                 explicit button, since auto-scroll alone isn't always obvious/reliable. --}}
            <div id="infinite-scroll-sentinel" class="py-8 text-center">
                <div id="infinite-scroll-loading" class="hidden items-center justify-center gap-2 text-sm text-gray-400">
                    <svg class="animate-spin h-4 w-4" style="color: var(--teal)" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ __('db.Loading more profiles…') }}
                </div>
                <button id="infinite-scroll-load-more" type="button" class="{{ $paginated->hasMorePages() ? '' : 'hidden' }} text-sm font-semibold px-6 py-2.5 rounded-lg border-2 hover:bg-teal-50 transition" style="color: var(--teal); border-color: var(--teal)">
                    {{ __('db.Load More Profiles') }} ↓
                </button>
                <p id="infinite-scroll-end" class="hidden text-sm text-gray-400">{{ __("db.You've reached the end — that's everyone matching your filters.") }}</p>
            </div>
        </div>
    </div>

    {{-- layouts/app.blade.php has no @stack('scripts'), so this has to live
         inline in the slot rather than @push, or it would silently vanish. --}}
    <script>
        (function () {
            const grid = document.getElementById('profile-grid');
            const sentinel = document.getElementById('infinite-scroll-sentinel');
            const loadingEl = document.getElementById('infinite-scroll-loading');
            const endEl = document.getElementById('infinite-scroll-end');
            const loadMoreBtn = document.getElementById('infinite-scroll-load-more');

            let page = 1;
            let hasMore = {{ $paginated->hasMorePages() ? 'true' : 'false' }};
            let loading = false;

            if (!hasMore) {
                endEl.classList.remove('hidden');
                return;
            }

            async function loadNextPage() {
                if (loading || !hasMore) return;
                loading = true;
                loadMoreBtn.classList.add('hidden');
                loadingEl.classList.remove('hidden');
                loadingEl.classList.add('flex');

                page += 1;
                const params = new URLSearchParams(window.location.search);
                params.set('page', page);

                try {
                    const res = await fetch('{{ route('nikah.browse') }}?' + params.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    });
                    const data = await res.json();

                    if (data.html && data.html.trim()) {
                        grid.insertAdjacentHTML('beforeend', data.html);
                    }
                    hasMore = data.has_more;
                } catch (e) {
                    hasMore = false;
                }

                loading = false;
                loadingEl.classList.add('hidden');
                loadingEl.classList.remove('flex');
                if (hasMore) {
                    loadMoreBtn.classList.remove('hidden');
                } else {
                    endEl.classList.remove('hidden');
                    observer.disconnect();
                }
            }

            loadMoreBtn.addEventListener('click', loadNextPage);

            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) loadNextPage();
            }, { rootMargin: '400px' });

            observer.observe(sentinel);
        })();
    </script>

    <x-faq-section module="nikah" />
</x-app-layout>
