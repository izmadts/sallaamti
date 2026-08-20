<x-guest-layout :title="$post->title" :description="$post->excerpt" :image="$post->cover_image ? Storage::url($post->cover_image) : null">

    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 240px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-3xl mx-auto px-4 py-14 relative z-10 text-center w-full">
            @if ($post->status !== 'published')
            <span class="inline-block bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full mb-3 uppercase tracking-wide">
                Preview — {{ ucfirst($post->status) }}, not yet public
            </span>
            @endif
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mt-2 mb-3">{{ $post->title }}</h1>
            <p class="text-white/70 text-sm">
                By <a href="{{ route('posts.by-author', $post->author) }}" class="underline hover:text-white">{{ $post->author->name }}</a>
                — {{ $post->published_at?->format('d M Y') ?? $post->created_at->format('d M Y') }}
            </p>
        </div>
    </section>

    <section class="py-14 bg-white">
        <div class="max-w-3xl mx-auto px-4">
            @if ($post->cover_image)
            <img src="{{ Storage::url($post->cover_image) }}" class="w-full h-80 object-cover rounded-2xl mb-10 shadow-sm" alt="{{ $post->title }}">
            @endif

            <div class="prose max-w-none text-gray-700 leading-relaxed">
                {!! nl2br(e($post->body)) !!}
            </div>

            @auth
            @if (Auth::id() === $post->user_id || Auth::user()->can('posts.manage'))
            <div class="mt-8 flex gap-3 text-sm">
                <a href="{{ route('posts.edit', $post) }}" class="text-teal-700 hover:underline">✏️ Edit</a>
                <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post permanently?')">
                    @csrf @method('DELETE')
                    <button class="text-red-500 hover:underline">🗑️ Delete</button>
                </form>
            </div>
            @endif
            @endauth

            @if ($post->status === 'published')
            <div class="mt-10 bg-teal-50 border border-teal-100 rounded-lg p-5">
                <h3 class="font-semibold text-gray-700 mb-3">📤 Share this Post</h3>
                <div class="flex gap-2 items-center mb-3">
                    <input type="text" readonly value="{{ route('posts.show', $post) }}" id="post-share-link" class="flex-1 border-gray-300 rounded-md text-xs bg-white">
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('post-share-link').value); this.innerText='✅ Copied'; setTimeout(() => this.innerText='Copy', 1500)"
                        class="bg-gray-100 text-gray-700 text-xs px-3 py-2 rounded hover:bg-gray-200">Copy</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="https://wa.me/?text={{ urlencode($post->title . ' — ' . route('posts.show', $post)) }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 bg-green-500 text-white text-sm px-4 py-2 rounded-lg hover:bg-green-600">WhatsApp</a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('posts.show', $post)) }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">Facebook</a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(route('posts.show', $post)) }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 bg-gray-800 text-white text-sm px-4 py-2 rounded-lg hover:bg-gray-900">X / Twitter</a>
                </div>
            </div>
            @endif

            {{-- Author bio card --}}
            <div class="mt-10 flex items-start gap-4 bg-gray-50 rounded-xl p-5">
                <img src="{{ $post->author->avatarUrl() }}" class="w-14 h-14 rounded-full flex-shrink-0" alt="{{ $post->author->name }}">
                <div>
                    <a href="{{ route('posts.by-author', $post->author) }}" class="font-semibold text-gray-800 hover:text-teal-700">{{ $post->author->name }}</a>
                    @if ($post->author->public_bio)
                    <p class="text-sm text-gray-500 mt-1">{{ $post->author->public_bio }}</p>
                    @endif
                    <a href="{{ route('posts.by-author', $post->author) }}" class="text-xs text-teal-700 hover:underline mt-1 inline-block">View all posts by {{ $post->author->name }} →</a>
                </div>
            </div>

            @if ($morePosts->isNotEmpty())
            <div class="mt-10 pt-6 border-t border-gray-100">
                <h3 class="font-semibold text-gray-700 mb-4">More from {{ $post->author->name }}</h3>
                <div class="grid sm:grid-cols-3 gap-4">
                    @foreach ($morePosts as $more)
                    <a href="{{ route('posts.show', $more) }}" class="text-sm text-gray-700 hover:text-teal-700 bg-gray-50 rounded-lg p-3">
                        {{ $more->title }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mt-10">
                <a href="{{ route('posts.index') }}" class="btn-base btn-teal inline-block text-sm px-5 py-2">← Back to Community Posts</a>
            </div>
        </div>
    </section>

    <style>
        .btn-base { display: inline-block; border-radius: 0.5rem; text-decoration: none; text-align: center; font-weight: 500; transition: all .2s ease; }
        .btn-teal { background: #0d6b6b; color: #fff !important; }
        .btn-teal:hover { background: #095555; }
    </style>
</x-guest-layout>
