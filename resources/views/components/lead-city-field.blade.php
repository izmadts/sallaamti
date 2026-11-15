@props([
    'name' => 'city',
    'value' => '',
    'label' => 'City',
])

{{-- Live-search against the same /api/v1/meta/cities endpoint the Nikah
     wizard's <x-country-state-city-fields> uses — scoped to just City
     (no Country/State here), a smaller standalone copy of that component's
     search logic rather than reusing it directly, since leads don't carry
     country/state at all. Free text still works if a real city isn't in
     the results; Lead::normalizeCity() cleans it up server-side either way.
     This plain-text box was the likely source of the "city problems"
     complaint that started this feature — see the 15 Nov 2026 city-wise
     lead assignment backlog item. --}}
<div x-data="{
        city: @js($value ?? ''),
        open: false,
        results: [],
        debounce: null,
        search() {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                fetch('/api/v1/meta/cities?' + new URLSearchParams({ search: this.city }))
                    .then(r => r.json())
                    .then(data => { this.results = data.cities || []; })
                    .catch(() => { this.results = []; });
            }, 250);
        },
        pick(c) { this.city = c; this.open = false; },
     }">
    <x-input-label for="{{ $name }}" :value="$label" />
    <div class="relative" @click.outside="open = false">
        <input type="text" id="{{ $name }}" name="{{ $name }}" x-model="city"
            @input="search()" @focus="open = true; search()" @keydown.escape="open = false"
            autocomplete="off" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1"
            placeholder="Type to search city (e.g. Karachi)">
        <div x-show="open && results.length" x-cloak
            class="absolute z-20 mt-1 w-full max-h-52 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg text-sm">
            <template x-for="opt in results" :key="opt">
                <div @mousedown.prevent="pick(opt)" x-text="opt" class="px-3 py-2 cursor-pointer hover:bg-teal-50"></div>
            </template>
        </div>
    </div>
</div>
