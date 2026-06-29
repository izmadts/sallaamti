<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Edit Lesson — {{ $lesson->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label value="Lesson Title" />
                    <x-text-input name="title" class="w-full mt-1" value="{{ old('title', $lesson->title) }}" required />
                </div>
                <div>
                    <x-input-label value="Video URL (optional, embed link)" />
                    <x-text-input name="video_url" class="w-full mt-1" value="{{ old('video_url', $lesson->video_url) }}" placeholder="https://www.youtube.com/embed/..." />
                </div>
                <div>
                    <x-input-label value="Content / Text" />
                    <textarea name="content" rows="8" class="border-gray-300 rounded-md w-full mt-1">{{ old('content', $lesson->content) }}</textarea>
                </div>
                <div>
                    <x-input-label value="Order" />
                    <x-text-input name="order" type="number" class="w-full mt-1" value="{{ old('order', $lesson->order) }}" />
                </div>
                <x-primary-button>Update Lesson</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>