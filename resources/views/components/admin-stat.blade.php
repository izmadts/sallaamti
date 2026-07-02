{{-- resources/views/components/admin-stat.blade.php --}}
{{--
    Props:
        href    - optional link, wraps card if provided
        value   - the number/value to display
        label   - description label below the number
        color   - tailwind color name: indigo, pink, yellow, green, teal, purple, blue, emerald, sky, gray, violet
        icon    - emoji icon
        badge   - boolean, shows "Needs action" badge when true
--}}
@props([
'href' => null,
'value' => 0,
'label' => '',
'color' => 'gray',
'icon' => '📊',
'badge' => false,
])

@php
$colorMap = [
'indigo' => ['border' => 'border-indigo-400', 'text' => 'text-indigo-600', 'bg' => 'bg-indigo-50', 'badge' => 'bg-indigo-100 text-indigo-700'],
'pink' => ['border' => 'border-pink-400', 'text' => 'text-pink-600', 'bg' => 'bg-pink-50', 'badge' => 'bg-pink-100 text-pink-700'],
'yellow' => ['border' => 'border-yellow-400', 'text' => 'text-yellow-600', 'bg' => 'bg-yellow-50', 'badge' => 'bg-yellow-100 text-yellow-700'],
'green' => ['border' => 'border-green-400', 'text' => 'text-green-600', 'bg' => 'bg-green-50', 'badge' => 'bg-green-100 text-green-700'],
'teal' => ['border' => 'border-teal-400', 'text' => 'text-teal-600', 'bg' => 'bg-teal-50', 'badge' => 'bg-teal-100 text-teal-700'],
'purple' => ['border' => 'border-purple-400', 'text' => 'text-purple-600', 'bg' => 'bg-purple-50', 'badge' => 'bg-purple-100 text-purple-700'],
'violet' => ['border' => 'border-violet-400', 'text' => 'text-violet-600', 'bg' => 'bg-violet-50', 'badge' => 'bg-violet-100 text-violet-700'],
'blue' => ['border' => 'border-blue-400', 'text' => 'text-blue-600', 'bg' => 'bg-blue-50', 'badge' => 'bg-blue-100 text-blue-700'],
'emerald' => ['border' => 'border-emerald-400', 'text' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'badge' => 'bg-emerald-100 text-emerald-700'],
'sky' => ['border' => 'border-sky-400', 'text' => 'text-sky-600', 'bg' => 'bg-sky-50', 'badge' => 'bg-sky-100 text-sky-700'],
'gray' => ['border' => 'border-gray-300', 'text' => 'text-gray-600', 'bg' => 'bg-gray-50', 'badge' => 'bg-gray-100 text-gray-700'],
];
$c = $colorMap[$color] ?? $colorMap['gray'];
$tag = $href ? 'a' : 'div';
$attrs = $href ? "href=\"{$href}\"" : '';
@endphp

<{{ $tag }} {{ $attrs }}
    class="bg-white rounded-lg border-l-4 {{ $c['border'] }} shadow-sm p-4
           {{ $href ? 'hover:shadow-md hover:bg-gray-50 transition cursor-pointer' : '' }}">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-2xl font-bold {{ $c['text'] }}">{{ $value }}</p>
            <p class="text-xs text-gray-500 mt-0.5 leading-tight">{{ $label }}</p>
        </div>
        <span class="text-xl opacity-60">{{ $icon }}</span>
    </div>
    @if ($badge)
    <span class="inline-block mt-2 text-xs px-1.5 py-0.5 rounded-full {{ $c['badge'] }} font-medium">
        Needs action
    </span>
    @endif
</{{ $tag }}>