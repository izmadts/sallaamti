@props(['title', 'icon' => '📋', 'color' => 'blue', 'description' => null])

@php
$palettes = [
    'blue' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-900', 'badge' => 'bg-blue-600'],
    'amber' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-900', 'badge' => 'bg-amber-600'],
    'emerald' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-900', 'badge' => 'bg-emerald-600'],
    'indigo' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-900', 'badge' => 'bg-indigo-600'],
    'purple' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-900', 'badge' => 'bg-purple-600'],
    'sky' => ['bg' => 'bg-sky-50', 'border' => 'border-sky-200', 'text' => 'text-sky-900', 'badge' => 'bg-sky-600'],
    'rose' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-900', 'badge' => 'bg-rose-600'],
    'teal' => ['bg' => 'bg-teal-50', 'border' => 'border-teal-200', 'text' => 'text-teal-900', 'badge' => 'bg-teal-600'],
];
$p = $palettes[$color] ?? $palettes['blue'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-xl border {$p['border']} {$p['bg']} p-5"]) }}>
    <div class="flex items-center gap-3 mb-4">
        <span class="flex items-center justify-center w-8 h-8 rounded-full {{ $p['badge'] }} text-white text-sm shrink-0">{{ $icon }}</span>
        <div>
            <h3 class="font-semibold {{ $p['text'] }}">{{ $title }}</h3>
            @if ($description)
            <p class="text-xs text-gray-500 mt-0.5">{{ $description }}</p>
            @endif
        </div>
    </div>
    {{ $slot }}
</div>
