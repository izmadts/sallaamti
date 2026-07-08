<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.blog-posts.index') }}" class="text-gray-400 hover:text-gray-600">Blog Posts</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Add Post</span>
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

        <form method="POST" action="{{ route('admin.blog-posts.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <x-input-label value="Title" />
                <x-text-input name="title" class="w-full mt-1" value="{{ old('title') }}" required />
            </div>
            <div>
                <x-input-label value="Category (optional)" />
                <x-text-input name="category" class="w-full mt-1" value="{{ old('category') }}" placeholder="Islamic Living, Quran, Community..." />
            </div>
            <div>
                <x-input-label value="Excerpt (optional, short summary shown on the blog list)" />
                <textarea name="excerpt" rows="2" class="border-gray-300 rounded-md w-full mt-1">{{ old('excerpt') }}</textarea>
            </div>
            <div>
                <x-input-label value="Cover Image (optional)" />
                <input type="file" name="cover_image" accept="image/*" class="w-full mt-1">
            </div>
            <div>
                <x-input-label value="Content" />
                <textarea name="content" rows="12" class="border-gray-300 rounded-md w-full mt-1" required>{{ old('content') }}</textarea>
            </div>
            <p class="text-xs text-gray-400">New posts are saved as a draft. A manager or admin will publish it once it's ready.</p>
            <x-primary-button>Save Draft</x-primary-button>
        </form>
    </div>
</x-admin-layout>
