@if (isset($profileCompletionPercent) && $profileCompletionPercent < 100)
@php
    $missing = $profileMissingFields ?? [];
    $firstAnchor = $missing[0]['anchor'] ?? null;
    $completeUrl = route('nikah.edit') . ($firstAnchor ? '#' . $firstAnchor : '');
@endphp
<div class="bg-amber-50 border-b border-amber-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[200px]">
            <p class="text-sm text-amber-800 font-medium">
                {{ __('db.Your Nikah profile is :percent% complete', ['percent' => $profileCompletionPercent]) }}
            </p>
            <div class="w-full bg-amber-200 rounded-full h-1.5 mt-1 max-w-md">
                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $profileCompletionPercent }}%"></div>
            </div>
            @if (count($missing))
            <p class="text-xs text-amber-700 mt-1.5">
                {{ __('db.Still missing:') }}
                @foreach ($missing as $i => $field)
                <a href="{{ route('nikah.edit') . '#' . $field['anchor'] }}" class="underline hover:no-underline font-medium">{{ __('db.' . $field['label']) }}</a>{{ $i < count($missing) - 1 ? ', ' : '' }}
                @endforeach
            </p>
            @endif
        </div>
        <a href="{{ $completeUrl }}"
            class="text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 px-3 py-1.5 rounded-md transition whitespace-nowrap">
            {{ __('db.Complete Your Nikah Profile') }}
        </a>
    </div>
</div>
@endif
