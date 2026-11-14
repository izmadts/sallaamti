@php($share = 'https://wa.me/?text=' . rawurlencode($item->title . ' — سلامتی کا مفت کتابچہ: ' . route('library.show', $item)))
<div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300" style="border:1px solid #eef2ef; display:flex; flex-direction:column">
    <a href="{{ route('library.show', $item) }}" class="block" style="background:#022c22">
        @if ($item->coverUrl())
            <img src="{{ $item->coverUrl() }}" alt="{{ $item->title }}" loading="lazy" class="w-full object-cover" style="height:220px">
        @else
            <div class="w-full flex items-center justify-center text-center px-4" style="height:220px; color:#e9c46a; font-size:22px; line-height:1.8">{{ $item->title }}</div>
        @endif
    </a>
    <div class="p-5" style="flex:1; display:flex; flex-direction:column">
        <a href="{{ route('library.show', $item) }}" class="block text-lg font-bold text-gray-800 mb-2" style="line-height:1.8">{{ $item->title }}</a>
        @if ($item->description)
            <p class="text-gray-500 text-sm line-clamp-3 mb-3" style="line-height:1.9">{{ $item->description }}</p>
        @endif
        <p class="text-xs text-gray-400 mb-4">@if($item->pages){{ $item->pages }} صفحات · @endif PDF · مفت</p>
        <div class="flex gap-2" style="margin-top:auto">
            <a href="{{ route('library.download', $item) }}" class="text-sm font-semibold text-white rounded-lg text-center" style="background:#065f46; padding:12px 14px; flex:1; min-height:44px">⬇ ڈاؤن لوڈ</a>
            <a href="{{ $share }}" target="_blank" rel="noopener" class="text-sm font-semibold rounded-lg text-center" style="background:#dcfce7; color:#166534; padding:12px 14px; flex:1; min-height:44px">واٹس ایپ پر بھیجیں</a>
        </div>
    </div>
</div>
