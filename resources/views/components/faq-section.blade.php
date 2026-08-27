@props(['module', 'title' => null])

@php
    $locale = app()->getLocale();
    $faqs = \App\Models\Faq::active()->forModule($module)->orderBy('sort_order')->get();
@endphp

@if ($faqs->isNotEmpty())
<section class="py-10" style="background: #fbfaf7;" {{ $isRtl ?? false ? 'dir=rtl' : '' }}>
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-6">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[--gold,#b8962e]/10 text-2xl mb-2">❓</span>
            <h2 class="text-2xl font-extrabold text-gray-800">{{ $title ?? ($locale === 'ur' ? 'اکثر پوچھے گئے سوالات' : __('db.Frequently Asked Questions')) }}</h2>
        </div>

        <div class="space-y-3" x-data="{ open: 0 }">
            @foreach ($faqs as $i => $faq)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition hover:shadow-md">
                <button type="button" @click="open = (open === {{ $i }} ? -1 : {{ $i }})"
                    class="w-full flex items-center justify-between gap-3 px-5 py-4 text-left rtl:text-right">
                    <span class="font-semibold text-gray-800">{{ $faq->question($locale) }}</span>
                    <span class="flex-shrink-0 w-7 h-7 rounded-full bg-teal-50 text-[--teal,#0d6b6b] flex items-center justify-center text-lg transition"
                        :class="open === {{ $i }} ? 'rotate-45' : ''">+</span>
                </button>
                <div x-show="open === {{ $i }}" x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="px-5 pb-4 prose prose-sm max-w-none text-gray-600 leading-relaxed">
                    {!! $faq->answer($locale) !!}
                </div>
            </div>
            @endforeach
        </div>

        <p class="text-center text-sm text-gray-400 mt-6">
            {{ $locale === 'ur' ? 'مزید سوال ہے؟' : __('db.Still have a question?') }}
            <a href="{{ url('/contact') }}" class="text-[--teal,#0d6b6b] font-medium hover:underline">{{ $locale === 'ur' ? 'ہم سے رابطہ کریں' : __('db.Contact us') }}</a>
        </p>
    </div>
</section>
@endif
