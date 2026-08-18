@props(['type' => 'success'])

@php
$styles = [
    'success' => 'bg-green-50 border-green-500 text-green-700',
    'error' => 'bg-red-50 border-red-500 text-red-700',
    'info' => 'bg-blue-50 border-blue-500 text-blue-700',
    'warning' => 'bg-yellow-50 border-yellow-500 text-yellow-800',
][$type] ?? 'bg-gray-50 border-gray-400 text-gray-700';

$icon = [
    'success' => '✅',
    'error' => '⚠️',
    'info' => 'ℹ️',
    'warning' => '⚠️',
][$type] ?? 'ℹ️';
@endphp

<div {{ $attributes->merge(['class' => "rounded-xl border-l-4 shadow-sm p-4 flex items-start gap-3 text-sm $styles"]) }} role="{{ $type === 'error' ? 'alert' : 'status' }}">
    <span class="shrink-0 text-base" aria-hidden="true">{{ $icon }}</span>
    <span>{{ $slot }}</span>
</div>
