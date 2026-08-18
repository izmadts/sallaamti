<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Community Posts</span>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-4">

        <div class="flex justify-between items-center flex-wrap gap-3">
            <div class="flex gap-2 overflow-x-auto">
                <a href="{{ route('admin.community-posts.index') }}"
                    class="text-xs font-semibold px-3 py-1.5 rounded-full border {{ !$activeTag ? 'bg-teal-700 text-white border-teal-700' : 'border-gray-200 text-gray-600 hover:border-teal-400' }}">
                    All
                </a>
                @foreach ($tags as $tag)
                <a href="{{ route('admin.community-posts.index', ['tag' => $tag]) }}"
                    class="text-xs font-semibold px-3 py-1.5 rounded-full border {{ $activeTag === $tag ? 'bg-teal-700 text-white border-teal-700' : 'border-gray-200 text-gray-600 hover:border-teal-400' }}">
                    #{{ $tag }}
                </a>
                @endforeach
            </div>
            <a href="{{ route('admin.community-posts.create') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800 flex-shrink-0">+ New Post</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($posts as $post)
            <div class="p-4 flex gap-4 items-start">
                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                    @if ($post->photo)
                    <img src="{{ Storage::url($post->photo) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xl">📣</div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">
                        @if ($post->is_pinned)
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 align-middle">📌 Pinned</span>
                        @endif
                        {{ $post->title }}
                    </p>
                    @if ($post->event_at)
                    <p class="text-xs text-gray-400 mb-1">🗓️ {{ $post->event_at->format('d M Y, g:i A') }}</p>
                    @endif
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $post->body }}</p>
                    @if (!empty($post->tags))
                    <div class="flex flex-wrap gap-1 mt-1">
                        @foreach ($post->tags as $tag)
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">#{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if ($post->socialDispatches->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @foreach ($post->socialDispatches as $dispatch)
                        <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-full
                            {{ $dispatch->status === 'sent' ? 'bg-green-100 text-green-700' : ($dispatch->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}"
                            title="{{ $dispatch->error_message }}">
                            {{ ucfirst($dispatch->platform) }}: {{ $dispatch->status }}
                            @if ($dispatch->status === 'failed')
                            <form method="POST" action="{{ route('admin.social-dispatches.retry', $dispatch) }}" class="inline">
                                @csrf
                                <button class="underline">retry</button>
                            </form>
                            @endif
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $post->status === 'published' ? 'Published' : 'Draft' }}
                    </span>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.community-posts.toggle', $post) }}">
                            @csrf
                            <button class="text-xs {{ $post->status === 'published' ? 'text-orange-500' : 'text-green-600' }} hover:underline">
                                {{ $post->status === 'published' ? 'Unpublish' : 'Publish' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.community-posts.pin', $post) }}">
                            @csrf
                            <button class="text-xs {{ $post->is_pinned ? 'text-amber-600' : 'text-gray-500' }} hover:underline">
                                {{ $post->is_pinned ? 'Unpin' : 'Pin' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.community-posts.edit', $post) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.community-posts.destroy', $post) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="p-5 text-gray-400">No posts yet.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>
