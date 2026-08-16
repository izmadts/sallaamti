@props(['name', 'id' => null, 'options' => [], 'value' => '', 'required' => false])

{{-- A single field that behaves like a native <select> (click it, scroll the
     list, pick an option) but also lets you type to filter that list down,
     or type something not on the list at all — the value that ends up in
     the form is always just whatever's in the text box. --}}
<div x-data="{
        open: false,
        query: @js($value ?? ''),
        options: @js($options),
        get filtered() {
            if (!this.query) return this.options;
            const q = this.query.toLowerCase();
            return this.options.filter(o => o.toLowerCase().includes(q));
        },
        pick(opt) { this.query = opt; this.open = false; $refs.input.focus(); },
     }"
    @click.outside="open = false" class="relative">
    <div class="relative">
        <input type="text" x-ref="input" id="{{ $id ?? $name }}" name="{{ $name }}" x-model="query"
            @focus="open = true" @click="open = true" @keydown.escape="open = false"
            autocomplete="off" {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1 pr-8']) }}>
        <button type="button" @click="open = !open; $refs.input.focus()" tabindex="-1"
            class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400 hover:text-gray-600">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
    <div x-show="open && filtered.length" x-cloak
        class="absolute z-20 mt-1 w-full max-h-52 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg text-sm">
        <template x-for="opt in filtered" :key="opt">
            <div @mousedown.prevent="pick(opt)" x-text="opt" class="px-3 py-2 cursor-pointer hover:bg-teal-50"></div>
        </template>
    </div>
</div>
