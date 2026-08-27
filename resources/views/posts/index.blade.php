<x-guest-layout :title="__('db.Community Posts & Stories')" :description="__('db.Real stories, reflections, and announcements shared by the Sallaamti community.')">

    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 220px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-4xl mx-auto px-4 py-14 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color:#e0c26b;">{{ __('db.Community Stories') }}</span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mt-2 mb-3">{{ __('db.Posts from the Sallaamti Community') }}</h1>
            <p class="text-white/70 text-sm max-w-xl mx-auto">{{ __('db.Stories, reflections, and announcements shared by our members — reviewed by our team before going live.') }}</p>
            @auth
            <a href="{{ route('posts.create') }}" class="inline-block mt-5 bg-white text-teal-800 font-semibold text-sm px-5 py-2.5 rounded-lg hover:opacity-90 transition">✍️ {{ __('db.Share Your Story') }}</a>
            @else
            <a href="{{ route('login') }}" class="inline-block mt-5 bg-white text-teal-800 font-semibold text-sm px-5 py-2.5 rounded-lg hover:opacity-90 transition">{{ __('db.Log In to Share a Post') }}</a>
            @endauth
        </div>
    </section>

    <section class="py-14 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4">
            @if ($posts->isEmpty())
            <p class="text-center text-gray-500 py-10">{{ __('db.No posts yet — be the first to share something with the community.') }}</p>
            @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($posts as $post)
                <a href="{{ route('posts.show', $post) }}" class="bg-white rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden flex flex-col">
                    @if ($post->cover_image)
                    <img src="{{ Storage::url($post->cover_image) }}" class="w-full h-40 object-cover" alt="{{ $post->title }}">
                    @else
                    <div class="w-full h-40 bg-gradient-to-br from-teal-600 to-teal-900 flex items-center justify-center text-4xl">📝</div>
                    @endif
                    <div class="p-5 flex-1 flex flex-col">
                        <h2 class="font-bold text-gray-800 mb-2 line-clamp-2">{{ $post->title }}</h2>
                        <p class="text-sm text-gray-500 line-clamp-3 flex-1">{{ $post->excerpt }}</p>
                        <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
                            <img src="{{ $post->author->avatarUrl() }}" class="w-6 h-6 rounded-full" alt="{{ $post->author->name }}">
                            <span class="text-xs text-gray-600">{{ $post->author->name }}</span>
                            <span class="text-xs text-gray-400 ml-auto">{{ $post->published_at?->format('d M Y') }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
            @endif
        </div>
    </section>

    <style>
        .section-eyebrow { font-size: 12px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; display: block; margin-bottom: 8px; }
    </style>
</x-guest-layout>
