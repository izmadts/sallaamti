@php($share = 'https://wa.me/?text=' . rawurlencode($item->title . ' — سلامتی کا مفت کتابچہ: ' . route('library.show', $item)))
<x-guest-layout :title="$item->title . ' — مفت کتابچہ | Sallaamti'" :description="\Illuminate\Support\Str::limit($item->description ?: $item->title . ' — سلامتی کا مفت اردو کتابچہ', 155)">
    <section class="py-8 md:py-14" style="background:#fdfbf5" dir="rtl">
        <div class="max-w-5xl mx-auto px-4">
            <nav class="text-sm text-gray-400 mb-5"><a href="{{ route('library.index') }}" style="color:#065f46">کتب خانہ</a> / {{ $item->categoryLabel() }}</nav>
            <div class="grid md:grid-cols-2 gap-6 items-start">
                <div class="rounded-2xl overflow-hidden shadow-sm" style="background:#022c22">
                    @if ($item->coverUrl())
                        <img src="{{ $item->coverUrl() }}" alt="{{ $item->title }}" class="w-full object-cover">
                    @else
                        <div class="flex items-center justify-center text-center px-6" style="min-height:320px; color:#e9c46a; font-size:26px; line-height:1.8">{{ $item->title }}</div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl md:text-4xl font-bold mb-4" style="color:#065f46; line-height:1.7">{{ $item->title }}</h1>
                    @if ($item->description)
                        <p class="text-gray-600 mb-4" style="line-height:2">{{ $item->description }}</p>
                    @endif
                    <p class="text-sm text-gray-400 mb-6">@if($item->pages){{ $item->pages }} صفحات · @endif PDF · مفت</p>
                    <div class="flex flex-col gap-3" style="max-width:420px">
                        <a href="{{ route('library.download', $item) }}" class="font-semibold text-white rounded-xl text-center" style="background:#065f46; padding:14px; min-height:48px">⬇ مفت ڈاؤن لوڈ کریں</a>
                        <a href="{{ $item->fileUrl() }}" target="_blank" rel="noopener" class="font-semibold rounded-xl text-center" style="background:#fff; color:#065f46; border:1px solid #a7f3d0; padding:14px; min-height:48px">📖 آن لائن پڑھیں</a>
                        <a href="{{ $share }}" target="_blank" rel="noopener" class="font-semibold rounded-xl text-center" style="background:#dcfce7; color:#166534; padding:14px; min-height:48px">واٹس ایپ پر شیئر کریں</a>
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
</x-guest-layout>
