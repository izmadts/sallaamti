<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-700 font-semibold">Blog Posts</span>
        </div>
    </x-slot>

    <div class="max-w-5xl space-y-4">
        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">✅ {{ session('status') }}</div>
        @endif

        <div class="flex justify-end">
            <a href="{{ route('admin.blog-posts.create') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">+ Add Post</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($posts as $post)
            <div class="p-4 flex gap-4 items-start">
                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                    @if ($post->cover_image)
                    <img src="{{ Storage::url($post->cover_image) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xl">📝</div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ $post->title }}</p>
                    <p class="text-xs text-gray-400 mb-1">By {{ $post->author?->name ?? 'Unknown' }} — {{ $post->created_at->format('d M Y') }}</p>
                    @if ($post->excerpt)
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $post->excerpt }}</p>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $post->status === 'published' ? 'Published' : 'Draft' }}
                    </span>
                    <div class="flex gap-2">
                        @if (auth()->user()->hasAnyRole(['admin', 'manager']))
                        <form method="POST" action="{{ route($post->status === 'published' ? 'admin.blog-posts.unpublish' : 'admin.blog-posts.publish', $post) }}">
                            @csrf @method('PATCH')
                            <button class="text-xs {{ $post->status === 'published' ? 'text-orange-500' : 'text-green-600' }} hover:underline">
                                {{ $post->status === 'published' ? 'Unpublish' : 'Publish' }}
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('admin.blog-posts.edit', $post) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="p-5 text-gray-400">No blog posts yet.</p>
            @endforelse
        </div>

        {{ $posts->links() }}
    </div>
</x-admin-layout>
