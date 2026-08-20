<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">📝 My Posts</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('posts.create') }}" class="btn-base bg-teal-700 text-white text-sm px-4 py-2 rounded-md hover:bg-teal-800">+ Write a Post</a>
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($posts as $post)
                <div class="p-5 flex justify-between items-start flex-wrap gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800">{{ $post->title }}</p>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $post->excerpt }}</p>
                        @if ($post->status === 'rejected' && $post->rejection_reason)
                        <p class="text-xs text-red-600 mt-1.5">Reason: {{ $post->rejection_reason }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1.5">
                            {{ $post->created_at->format('d M Y') }}
                            @if ($post->status === 'published') · {{ $post->views_count }} views @endif
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs px-2 py-1 rounded-full
                                {{ $post->status === 'published' ? 'bg-green-100 text-green-800' :
                                   ($post->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($post->status) }}
                        </span>
                        <div class="flex gap-3 text-xs">
                            @if ($post->status === 'published')
                            <a href="{{ route('posts.show', $post) }}" class="text-teal-700 hover:underline">View</a>
                            @endif
                            <a href="{{ route('posts.edit', $post) }}" class="text-teal-700 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post permanently?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-500">You haven't written any posts yet.</p>
                @endforelse
            </div>

            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
