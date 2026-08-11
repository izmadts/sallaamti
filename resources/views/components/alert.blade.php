@props(['type' => 'success'])

@php
$styles = [
    'success' => 'bg-green-50 border-green-200 text-green-700',
    'error' => 'bg-red-50 border-red-200 text-red-700',
    'info' => 'bg-blue-50 border-blue-200 text-blue-700',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
][$type] ?? 'bg-gray-50 border-gray-200 text-gray-700';

$icon = [
    'success' => '✅',
    'error' => '⚠️',
    'info' => 'ℹ️',
    'warning' => '⚠️',
][$type] ?? 'ℹ️';
@endphp

<div {{ $attributes->merge(['class' => "rounded-lg border p-4 flex items-start gap-3 text-sm $styles"]) }} role="{{ $type === 'error' ? 'alert' : 'status' }}">
    <span class="shrink-0" aria-hidden="true">{{ $icon }}</span>
    <span>{{ $slot }}</span>
</div>
