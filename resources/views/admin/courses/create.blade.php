<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">New Course</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <x-input-label value="Title" />
                    <x-text-input name="title" class="w-full mt-1" required />
                </div>
                <div>
                    <x-input-label value="Description" />
                    <textarea name="description" rows="3" class="border-gray-300 rounded-md w-full mt-1"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Category" />
                        <x-text-input name="category" class="w-full mt-1" placeholder="Naazira / Tarjuma / Seerah" />
                    </div>
                    <div>
                        <x-input-label value="Level" />
                        <x-text-input name="level" class="w-full mt-1" placeholder="Beginner" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Recommended min age (optional)" />
                        <x-text-input type="number" name="min_age" min="1" max="120" class="w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Recommended max age (optional)" />
                        <x-text-input type="number" name="max_age" min="1" max="120" class="w-full mt-1" />
                    </div>
                </div>
                <div>
                    <x-input-label value="Thumbnail" />
                    <input type="file" name="thumbnail" accept="image/*" class="w-full mt-1">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" value="1" id="is_published">
                    <label for="is_published" class="text-sm">Publish immediately</label>
                </div>
                <x-primary-button>Create Course</x-primary-button>
            </form>
        </div>
    </div>
</x-admin-layout>