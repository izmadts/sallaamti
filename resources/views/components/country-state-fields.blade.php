@props([
    'countryName' => 'country',
    'stateName' => 'state',
    'countryValue' => '',
    'stateValue' => '',
    'allStates' => [],
    'countryRequired' => false,
    'labelClass' => 'block font-medium text-sm text-gray-700',
    'inputClass' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1',
])

{{-- Two fields sharing one Alpine scope so picking a Country immediately
     narrows the State list to that country's provinces/states (from
     App\Support\CountryStates) — same type-to-filter/click-to-pick feel as
     <x-searchable-select>, but that component's options are fixed at
     render time, so this reactive pairing is built separately here. Each
     field still accepts free text beyond the suggested list.
     `class="contents"` makes this wrapper invisible to CSS Grid, so the two
     fields below act as normal sibling grid cells in whatever grid this is
     dropped into. --}}
<div x-data="{
        allStates: @js($allStates),
        country: @js($countryValue),
        state: @js($stateValue),
        countryOpen: false,
        stateOpen: false,
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
</div>
