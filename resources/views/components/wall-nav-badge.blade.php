{{-- Wraps a Sallaamti Wall nav link/button with a hover (desktop) / tap
     (mobile) popover previewing the latest approved dua — the "come see
     what's happening" teaser that makes the nav entry worth noticing
     instead of just another menu item. --}}
@props(['dua'])
<div class="relative inline-block" x-data="{ showPreview: false }" @mouseenter="showPreview = true" @mouseleave="showPreview = false" @click.stop="showPreview = !showPreview" @click.outside="showPreview = false">
    {{ $slot }}
    @if ($dua)
    <div x-show="showPreview" x-cloak x-transition.opacity
        class="absolute z-50 top-full mt-2 start-0 w-64 bg-white rounded-xl shadow-xl border border-gray-100 p-3 text-left normal-case">
        <p class="text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--teal)">🤲 {{ __('db.Latest on the wall') }}</p>
        <p class="text-sm text-gray-700 leading-snug">{{ \Illuminate\Support\Str::limit($dua->body, 90) }}</p>
    </div>
    @endif
</div>
