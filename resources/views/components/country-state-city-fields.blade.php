@props([
    'countryName' => 'country',
    'stateName' => 'state',
    'cityName' => 'city',
    'countryValue' => '',
    'stateValue' => '',
    'cityValue' => '',
    'allStates' => [],
    'countryRequired' => false,
    'cityRequired' => false,
    'labelClass' => 'block font-medium text-sm text-gray-700',
    'inputClass' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1',
])

{{-- Country/State unchanged from <x-country-state-fields> (preloaded list,
     reactive narrowing). City is new: unlike Country/State it can't be
     preloaded (~150k rows in the cities table, see the create_cities_table
     migration) — it's a live-search combo hitting GET /api/v1/meta/cities,
     debounced, scoped to whichever country/state are currently picked. Same
     pick-or-type feel as the rest of this form (Education's
     <x-searchable-select>): a suggestion list, but free text still works if
     a real city isn't in the results. `class="contents"` keeps this
     wrapper invisible to CSS Grid, same as the two-field version. --}}
<div x-data="{
        allStates: @js($allStates),
        country: @js($countryValue),
        state: @js($stateValue),
        city: @js($cityValue),
        countryOpen: false,
        stateOpen: false,
        cityOpen: false,
        cityResults: [],
        cityDebounce: null,
        get countries() { return Object.keys(this.allStates).sort() },
        get filteredCountries() {
            if (!this.country) return this.countries;
            const q = this.country.toLowerCase();
            return this.countries.filter(c => c.toLowerCase().includes(q));
        },
        get stateOptions() { return this.allStates[this.country] || [] },
        get filteredStates() {
            if (!this.state) return this.stateOptions;
            const q = this.state.toLowerCase();
            return this.stateOptions.filter(s => s.toLowerCase().includes(q));
        },
        pickCountry(c) {
            this.country = c;
            this.countryOpen = false;
            if (!this.stateOptions.includes(this.state)) { this.state = ''; }
        },
        pickState(s) { this.state = s; this.stateOpen = false; },
        searchCities() {
            clearTimeout(this.cityDebounce);
            this.cityDebounce = setTimeout(() => {
                const params = new URLSearchParams({ search: this.city });
                if (this.country) params.set('country', this.country);
                if (this.state) params.set('state', this.state);
                fetch('/api/v1/meta/cities?' + params.toString())
                    .then(r => r.json())
                    .then(data => { this.cityResults = data.cities || []; })
                    .catch(() => { this.cityResults = []; });
            }, 250);
        },
        pickCity(c) { this.city = c; this.cityOpen = false; },
     }" class="contents">

    <div>
        <label for="{{ $countryName }}" class="{{ $labelClass }}">{{ __('db.Country') }}</label>
        <div class="relative" @click.outside="countryOpen = false">
            <input type="text" id="{{ $countryName }}" name="{{ $countryName }}" x-model="country"
                @focus="countryOpen = true" @click="countryOpen = true" @keydown.escape="countryOpen = false"
                autocomplete="off" {{ $countryRequired ? 'required' : '' }}
                class="{{ $inputClass }}"
                placeholder="{{ __('db.Type to search or select') }}"
                title="{{ __('db.The country you currently live in — type to search, or pick from the list.') }}">
            <div x-show="countryOpen && filteredCountries.length" x-cloak
                class="absolute z-20 mt-1 w-full max-h-52 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg text-sm">
                <template x-for="opt in filteredCountries" :key="opt">
                    <div @mousedown.prevent="pickCountry(opt)" x-text="opt" class="px-3 py-2 cursor-pointer hover:bg-teal-50"></div>
                </template>
            </div>
        </div>
    </div>

    <div>
        <label for="{{ $stateName }}" class="{{ $labelClass }}">{{ __('db.State / Province') }}</label>
        <div class="relative" @click.outside="stateOpen = false">
            <input type="text" id="{{ $stateName }}" name="{{ $stateName }}" x-model="state"
                @focus="stateOpen = true" @click="stateOpen = true" @keydown.escape="stateOpen = false"
                autocomplete="off"
                class="{{ $inputClass }}"
                :placeholder="stateOptions.length ? '{{ __('db.Type to search or select') }}' : '{{ __('db.Type your state / province') }}'"
                title="{{ __('db.Updates automatically to that country\'s states/provinces once you pick a Country above — you can still type your own if it\'s not listed.') }}">
            <div x-show="stateOpen && filteredStates.length" x-cloak
                class="absolute z-20 mt-1 w-full max-h-52 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg text-sm">
                <template x-for="opt in filteredStates" :key="opt">
                    <div @mousedown.prevent="pickState(opt)" x-text="opt" class="px-3 py-2 cursor-pointer hover:bg-teal-50"></div>
                </template>
            </div>
        </div>
    </div>

    <div>
        <label for="{{ $cityName }}" class="{{ $labelClass }}">{{ __('db.City') }}</label>
        <div class="relative" @click.outside="cityOpen = false">
            <input type="text" id="{{ $cityName }}" name="{{ $cityName }}" x-model="city"
                @input="searchCities()" @focus="cityOpen = true; searchCities()" @keydown.escape="cityOpen = false"
                autocomplete="off" {{ $cityRequired ? 'required' : '' }}
                class="{{ $inputClass }}"
                placeholder="{{ __('db.Type to search your city') }}"
                title="{{ __('db.The city you currently live in — type to search, or pick from the list.') }}">
            <div x-show="cityOpen && cityResults.length" x-cloak
                class="absolute z-20 mt-1 w-full max-h-52 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg text-sm">
                <template x-for="opt in cityResults" :key="opt">
                    <div @mousedown.prevent="pickCity(opt)" x-text="opt" class="px-3 py-2 cursor-pointer hover:bg-teal-50"></div>
                </template>
            </div>
        </div>
    </div>
</div>
