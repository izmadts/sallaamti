@php($share = 'https://wa.me/?text=' . rawurlencode($item->title . ' — سلامتی کا مفت کتابچہ: ' . route('library.show', $item)))
<x-guest-layout :title="$item->title . ' — مفت کتابچہ | Sallaamti'" :description="\Illuminate\Support\Str::limit($item->description ?: $item->title . ' — سلامتی کا مفت اردو کتابچہ', 155)">
    @push('schema')
    {{-- CreativeWork structured data — Google can show this booklet as a rich result. --}}
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $item->title,
            'description' => $item->description ?: $item->title,
            'url' => route('library.show', $item),
            'inLanguage' => $item->language,
            'isAccessibleForFree' => true,
            'encodingFormat' => 'application/pdf',
            ...($item->coverUrl() ? ['thumbnailUrl' => $item->coverUrl()] : []),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    {{-- Nastaliq-style Urdu titles for the Library pages specifically (owner
         spec asked for Jameel Noori Nastaleeq; that font isn't available to
         vendor here, so this is Google's own open-source Nastaliq-style
         Urdu font — same calligraphic look, no licensing risk). Pushed into
         the layout's one <head> stack rather than touching guest-layout
         itself, since no other page asked for this font. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">
    @endpush
    <section class="py-8 md:py-14" style="background:#fdfbf5" dir="rtl">
        <div class="max-w-5xl mx-auto px-4">
            <nav class="text-sm text-gray-400 mb-5"><a href="{{ route('library.index') }}" style="color:#065f46">کتب خانہ</a> / {{ $item->categoryLabel() }}</nav>
            <div class="grid md:grid-cols-2 gap-6 items-start">
                <div class="rounded-2xl overflow-hidden shadow-sm relative" style="background:#022c22">
                    @if ($item->isNew())
                    <span class="absolute top-3 right-3 text-xs font-bold px-2 py-1 rounded-full" style="background:#fef9c3; color:#854d0e; z-index:1">نئی ✨</span>
                    @endif
                    @if ($item->coverUrl())
                        <img src="{{ $item->coverUrl() }}" alt="{{ $item->title }}" class="w-full object-cover">
                    @else
                        <div class="flex items-center justify-center text-center px-6" style="min-height:320px; color:#e9c46a; font-size:26px; line-height:1.8">{{ $item->title }}</div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl md:text-4xl font-bold mb-4" style="color:#065f46; line-height:1.7; font-family:'Noto Nastaliq Urdu', serif">{{ $item->title }}</h1>
                    @if ($item->description)
                        <p class="text-gray-600 mb-4" style="line-height:2">{{ $item->description }}</p>
                    @endif
                    <p class="text-sm text-gray-400 mb-2">{{ $item->metaLine() }}</p>
                    @if ($item->review_note)
                    <p class="text-xs text-gray-400 mb-4">✅ {{ $item->review_note }}</p>
                    @endif
                    <div class="flex flex-col gap-3" style="max-width:420px">
                        <a href="{{ route('library.download', $item) }}" class="font-semibold text-white rounded-xl text-center" style="background:#065f46; padding:14px; min-height:48px">⬇ مفت ڈاؤن لوڈ کریں</a>
                        <a href="{{ $item->fileUrl() }}" target="_blank" rel="noopener" class="font-semibold rounded-xl text-center" style="background:#fff; color:#065f46; border:1px solid #a7f3d0; padding:14px; min-height:48px">📖 آن لائن پڑھیں</a>
                        <a href="{{ $share }}" target="_blank" rel="noopener" id="library-whatsapp-share" class="font-semibold rounded-xl text-center" style="background:#dcfce7; color:#166534; padding:14px; min-height:48px">واٹس ایپ پر شیئر کریں</a>
                        <button type="button" id="library-copy-link" data-url="{{ route('library.show', $item) }}" class="font-semibold rounded-xl text-center" style="background:#f3f4f6; color:#4b5563; padding:14px; min-height:48px; border:none; cursor:pointer">🔗 لنک کاپی کریں</button>
                    </div>
                    <p class="text-xs text-gray-400 mt-6" style="line-height:1.9">قرآن کا ترجمہ: بیان القرآن (ڈاکٹر اسرار احمدؒ)۔ یہ فتویٰ نہیں؛ کسی خاص مسئلے میں اپنے معتبر عالم سے رجوع کریں۔</p>
                </div>
            </div>

            @if ($more->isNotEmpty())
            <h2 class="text-xl font-bold mt-12 mb-5" style="color:#065f46">مزید کتابچے</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($more as $m) @include('library._card', ['item' => $m]) @endforeach
            </div>
            @endif
        </div>
    </section>

    <script>
        (function () {
            var eventUrl = '{{ route('api.v1.library.event', $item) }}';
            function fireShare() {
                if (navigator.sendBeacon) {
                    navigator.sendBeacon(eventUrl, new Blob([JSON.stringify({ type: 'share' })], { type: 'application/json' }));
                }
            }
            document.getElementById('library-whatsapp-share')?.addEventListener('click', fireShare);
            var copyBtn = document.getElementById('library-copy-link');
            copyBtn?.addEventListener('click', function () {
                navigator.clipboard.writeText(copyBtn.dataset.url).then(function () {
                    var original = copyBtn.innerHTML;
                    copyBtn.innerHTML = '✅ کاپی ہو گیا';
                    setTimeout(function () { copyBtn.innerHTML = original; }, 2000);
                    fireShare();
                });
            });
        })();
    </script>
</x-guest-layout>
