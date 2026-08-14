@props(['dark' => false, 'compact' => false])

@php
$languages = \Illuminate\Support\Facades\Cache::rememberForever('languages_list', fn () => \App\Models\Language::active()->orderBy('name')->get());
$current = $activeLanguage ?? \App\Models\Language::getDefaultLanguage();
@endphp

@if ($languages->count() > 1)
<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = ! open" type="button" aria-label="{{ __('db.Change language') }}"
        class="inline-flex items-center gap-1 rounded-full font-medium transition
               {{ $compact ? 'px-2.5 py-2 text-base' : 'px-2.5 py-1.5 text-sm' }}
               {{ $dark ? 'text-white hover:bg-white/10' : 'text-gray-600 hover:text-teal-700 hover:bg-gray-100' }}">
        <span class="text-lg leading-none">{{ $current?->flag ?: '🌐' }}</span>
        @unless ($compact)
        {{ $current?->name ?? __('db.Language') }}
        @endunless
        <svg class="ms-0.5 h-3.5 w-3.5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open" x-transition x-cloak
        class="absolute end-0 mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 py-1">
        @foreach ($languages as $language)
        <a href="{{ route('language.switch', $language->language) }}"
            class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100 {{ $current?->id === $language->id ? 'font-semibold text-teal-700' : 'text-gray-700' }}">
            <span class="text-base leading-none">{{ $language->flag ?: '🌐' }}</span>
            {{ $language->name }}
        </a>
        @endforeach
    </div>
</div>
@endif
