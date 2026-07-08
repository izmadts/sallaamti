<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.blog-posts.index') }}" class="text-gray-400 hover:text-gray-600">Blog Posts</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Edit Post</span>
        </div>
    </x-slot>

    <div class="max-w-2xl bg-white rounded-lg shadow-sm p-6">
        @if ($errors->any())
        <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.blog-posts.update', $post) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <x-input-label value="Title" />
                <x-text-input name="title" class="w-full mt-1" value="{{ old('title', $post->title) }}" required />
            </div>
            <div>
                <x-input-label value="Category (optional)" />
                <x-text-input name="category" class="w-full mt-1" value="{{ old('category', $post->category) }}" placeholder="Islamic Living, Quran, Community..." />
            </div>
            <div>
                <x-input-label value="Excerpt (optional, short summary shown on the blog list)" />
                <textarea name="excerpt" rows="2" class="border-gray-300 rounded-md w-full mt-1">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
            <div>
                <x-input-label value="Cover Image" />
                @if ($post->cover_image)
                <div class="mt-1 mb-2">
                    <img src="{{ Storage::url($post->cover_image) }}" class="w-32 h-20 object-cover rounded">
                </div>
                @endif
                <input type="file" name="cover_image" accept="image/*" class="w-full mt-1">
            </div>
            <div>
                <x-input-label value="Content" />
                <textarea name="content" rows="12" class="border-gray-300 rounded-md w-full mt-1" required>{{ old('content', $post->content) }}</textarea>
            </div>
            <div class="flex items-center gap-2 text-xs px-3 py-2 rounded-full inline-flex {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $post->status === 'published' ? '✅ Published' : '📝 Draft' }}
            </div>
            <p class="text-xs text-gray-400">Publishing status is controlled from the Blog Posts list, not from this form.</p>
            <x-primary-button>Save Changes</x-primary-button>
        </form>
    </div>
</x-admin-layout>
