@props(['steps', 'titles', 'current'])

@php
$currentIndex = array_search($current, $steps);
@endphp

<div class="mb-8">
    <div class="flex items-center">
        @foreach ($steps as $i => $step)
        @php $isDone = $i < $currentIndex; $isActive = $step === $current; @endphp
        <div class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold shrink-0
                    {{ $isActive ? 'bg-teal-700 text-white' : ($isDone ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-400') }}">
                    @if ($isDone) ✓ @else {{ $i + 1 }} @endif
                </div>
                <span class="text-[10px] mt-1 text-center max-w-[70px] leading-tight {{ $isActive ? 'text-teal-700 font-semibold' : 'text-gray-400' }} hidden sm:block">
                    {{ $titles[$step] ?? ucfirst($step) }}
                </span>
            </div>
            @if ($i < count($steps) - 1)
            <div class="flex-1 h-0.5 mx-1 {{ $isDone ? 'bg-teal-300' : 'bg-gray-200' }}"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>
