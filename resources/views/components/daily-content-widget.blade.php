@if ($dailyContent)
<div class="bg-white rounded-2xl shadow-sm border-t-4 overflow-hidden" style="border-color: var(--gold)">
    <div class="p-4 sm:p-5">
        <p class="text-[11px] font-bold uppercase tracking-widest" style="color: var(--gold)">
            {{ $dailyContent->type === 'ayah' ? '📖 ' . __('db.Ayah of the Day') : '🕌 ' . __('db.Hadith of the Day') }}
        </p>
        @if ($dailyContent->arabic_text)
        <p class="text-lg text-gray-800 mt-2 leading-relaxed" dir="rtl">{{ $dailyContent->arabic_text }}</p>
        @endif
        <div class="prose prose-sm max-w-none text-gray-700 mt-2 leading-relaxed">{!! $dailyContent->translation !!}</div>
        <p class="text-xs text-gray-400 mt-2 font-medium">— {{ $dailyContent->reference }}</p>
    </div>
</div>
@endif
