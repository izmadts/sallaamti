<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ isset($banner) ? 'Edit Banner' : 'New Banner' }}</h2>
    </x-slot>
    <div class="max-w-xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST"
                action="{{ isset($banner) ? route('admin.banners.update', $banner) : route('admin.banners.store') }}"
                enctype="multipart/form-data" class="space-y-4">
                @csrf
                @if (isset($banner)) @method('PUT') @endif

                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title', $banner->title ?? '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Subtitle</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle ?? '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Button Text</label>
                    <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text ?? '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Button URL</label>
                    <input type="text" name="button_url" value="{{ old('button_url', $banner->button_url ?? '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Order (1 = first)</label>
                    <input type="number" name="order" value="{{ old('order', $banner->order ?? 0) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Banner Image</label>
                    @if (isset($banner) && $banner->image)
                    <img src="{{ Storage::url($banner->image) }}" class="w-full h-32 object-cover rounded-lg mb-2">
                    @endif
                    <input type="file" name="image" accept="image/*"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                        {{ !isset($banner) ? 'required' : '' }}>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                        {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm text-gray-600">Active (show on homepage)</label>
                </div>
                <div class="flex justify-between items-center">
                    <a href="{{ route('admin.banners.index') }}" class="text-sm text-gray-400 hover:underline">← Back</a>
                    <button class="btn-base btn-teal px-6 py-2 font-semibold text-sm">
                        {{ isset($banner) ? 'Update' : 'Create' }} Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>