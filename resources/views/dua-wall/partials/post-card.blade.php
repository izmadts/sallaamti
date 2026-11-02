@php
    $displayName = $post->author->name ?? __('db.Sallaamti Team');
    $media = $post->mediaItems();
@endphp
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if (count($media) > 1)
    {{-- Instagram/Facebook-style swipeable carousel for a multi-photo/video post. --}}
    <div class="relative w-full h-44 sm:h-96 overflow-x-auto flex snap-x snap-mandatory" style="scroll-snap-type: x mandatory;">
        @foreach ($media as $item)
        <div class="flex-shrink-0 w-full h-full snap-center">
            @if ($item['type'] === 'video')
            <video src="{{ \Illuminate\Support\Facades\Storage::url($item['path']) }}" controls playsinline class="w-full h-full object-cover bg-black"></video>
            @else
            <img src="{{ \Illuminate\Support\Facades\Storage::url($item['path']) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            @endif
        </div>
        @endforeach
    </div>
    @elseif ($post->video)
    <video src="{{ \Illuminate\Support\Facades\Storage::url($post->video) }}" poster="{{ $post->photo ? \Illuminate\Support\Facades\Storage::url($post->photo) : '' }}" controls playsinline class="w-full max-h-96 bg-black"></video>
    @elseif ($post->photo)
    <img src="{{ \Illuminate\Support\Facades\Storage::url($post->photo) }}" alt="{{ $post->title }}" class="w-full h-44 object-cover">
    @endif
    <div class="p-4 sm:p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg flex-shrink-0" style="background: #fdf6e3">📣</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    @if ($post->is_pinned)
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">📌 {{ __('db.Pinned') }}</span>
                    @endif
                    <p class="font-semibold text-sm text-gray-800">{{ $displayName }}</p>
                    <span class="text-xs text-gray-400">{{ ($post->published_at ?? $post->created_at)->diffForHumans() }}</span>
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

                @if ($post->isPoll())
                @php
                    $myVote = auth()->check() ? $post->myPollVote(auth()->user()) : null;
                    $pollResults = $post->pollResults();
                    $hasVoted = $myVote !== null;
                @endphp
                <div class="mt-3" data-poll data-vote-url="{{ route('wall.post.vote', $post) }}" data-voted="{{ $hasVoted ? '1' : '0' }}">
                    @foreach ($pollResults as $option)
                    <button type="button" data-vote-option data-index="{{ $option['index'] }}"
                        {{ auth()->check() ? '' : 'disabled' }}
                        class="poll-option w-full relative overflow-hidden rounded-lg border mb-2 text-left text-sm font-medium px-3 py-2 transition {{ $myVote === $option['index'] ? 'border-teal-400 bg-teal-50/40' : 'border-gray-200 hover:border-gray-300' }} {{ auth()->check() ? '' : 'cursor-not-allowed opacity-60' }}">
                        <div class="poll-fill absolute inset-y-0 left-0 bg-teal-100 transition-all" style="width: {{ $hasVoted ? $option['percentage'] : 0 }}%"></div>
                        <div class="relative flex justify-between items-center gap-2">
                            <span>{{ $option['label'] }}</span>
                            <span class="poll-pct text-xs text-gray-500 font-semibold {{ $hasVoted ? '' : 'hidden' }}">{{ $option['percentage'] }}%</span>
                        </div>
                    </button>
                    @endforeach
                    <p class="text-xs text-gray-400 poll-total">
                        {{ collect($pollResults)->sum('votes') }} {{ \Illuminate\Support\Str::plural('vote', collect($pollResults)->sum('votes')) }}
                        @unless (auth()->check())
                        · <a href="{{ route('login') }}" class="underline">Log in to vote</a>
                        @endunless
                    </p>
                </div>
                @endif

                <div class="mt-3 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <x-reaction-picker :model="$post" :react-url="route('wall.post.react', $post)" />
                        <button type="button" data-comments-toggle data-thread-id="post-{{ $post->id }}" data-comments-url="{{ route('wall.post.comments', $post) }}"
                            class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-full border border-gray-200 text-gray-500 hover:border-gray-300">
                            💬 <span data-comments-count>{{ $post->comments_count ?? 0 }}</span>
                        </button>
                    </div>
                    <x-save-button :model="$post" :save-url="route('wall.post.save', $post)" />
                </div>
                <div data-comments-container data-thread-id="post-{{ $post->id }}" class="hidden mt-3"></div>
            </div>
        </div>
    </div>
</div>
