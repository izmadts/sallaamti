@php
    $displayName = $post->author->name ?? __('db.Sallaamti Team');
@endphp
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if ($post->photo)
    <img src="{{ \Illuminate\Support\Facades\Storage::url($post->photo) }}" alt="{{ $post->title }}" class="w-full h-44 object-cover">
    @endif
    <div class="p-4 sm:p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg flex-shrink-0" style="background: #fdf6e3">📣</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="font-semibold text-sm text-gray-800">{{ $displayName }}</p>
                    <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                </div>

                <h3 class="font-bold text-gray-800 mt-1.5">{{ $post->title }}</h3>
                <p class="text-gray-700 text-sm mt-1 whitespace-pre-line break-words">{{ $post->body }}</p>

                @if ($post->event_at)
                <p class="text-xs font-semibold mt-2" style="color: var(--teal)">🗓️ {{ $post->event_at->format('d M Y, g:i A') }}</p>
                @endif

                @if (!empty($post->tags))
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @foreach ($post->tags as $tag)
                    <a href="{{ route('wall.index', ['tag' => $tag]) }}" class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition">
                        #{{ $tag }}
                    </a>
                    @endforeach
                </div>
                @endif

                <div class="mt-3">
                    <x-reaction-picker :model="$post" :react-url="route('wall.post.react', $post)" />
                </div>
            </div>
        </div>
    </div>
</div>
