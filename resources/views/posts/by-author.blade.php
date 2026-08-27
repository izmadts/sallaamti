<x-guest-layout :title="__('db.:name — Sallaamti Community', ['name' => $user->name])" :description="$user->public_bio ?? __('db.:name\'s posts on Sallaamti.', ['name' => $user->name])">

    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 220px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-3xl mx-auto px-4 py-14 relative z-10 text-center w-full">
            <img src="{{ $user->avatarUrl() }}" class="w-20 h-20 rounded-full mx-auto mb-4 border-4 border-white/20" alt="{{ $user->name }}">
            <h1 class="text-2xl md:text-3xl font-extrabold text-white mb-2">{{ $user->name }}</h1>
            @if ($user->public_bio)
            <p class="text-white/70 text-sm max-w-lg mx-auto">{{ $user->public_bio }}</p>
            @endif
        </div>
    </section>

    <section class="py-14 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4">
            @if ($posts->isEmpty())
            <p class="text-center text-gray-500 py-10">{{ __('db.No published posts from :name yet.', ['name' => $user->name]) }}</p>
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
                        <p class="text-xs text-gray-400 mt-3">{{ $post->published_at?->format('d M Y') }}</p>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
            @endif

            <div class="mt-10 text-center">
                <a href="{{ route('posts.index') }}" class="text-sm text-teal-700 hover:underline">{{ __('db.← Browse all Community Posts') }}</a>
            </div>
        </div>
    </section>
</x-guest-layout>
