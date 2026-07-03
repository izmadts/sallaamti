<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.banners.index') }}" class="text-gray-400 hover:text-gray-600">Banners</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Edit Banner</span>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm p-6">
            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf 
                @method('PUT')
                <div>
                    <x-input-label value="Subtitle" />
                    <x-text-input name="subtitle" class="w-full mt-1" :value="old('subtitle', $banner->subtitle)" />
                </div>
                <div>
                    <x-input-label value="Title" />
                    <x-text-input name="title" class="w-full mt-1" :value="old('title', $banner->title)" required />
                </div>
                <div>
                    <x-input-label value="Description" />
                    <x-text-input name="description" class="w-full mt-1" :value="old('description', $banner->description)" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Button Text" />
                        <x-text-input name="button_text" class="w-full mt-1" :value="old('button_text', $banner->button_text)" />
                    </div>
                    <div>
                        <x-input-label value="Button URL" />
                        <x-text-input name="button_url" class="w-full mt-1" :value="old('button_url', $banner->button_url)" />
                    </div>
                </div>
                <div>
                    <x-input-label value="Background Image (leave blank to keep current)" />
                    <div class="mt-1 mb-2">
                        <img src="{{ str_starts_with($banner->image, 'img/') ? asset($banner->image) : Storage::url($banner->image) }}"
                            class="h-24 rounded object-cover">
                    </div>
                    <input type="file" name="image" accept="image/*" class="w-full">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                        {{ $banner->is_active ? 'checked' : '' }} class="rounded">
                    <label for="is_active" class="text-sm">Show this banner</label>
                </div>
                <x-primary-button>Update Banner</x-primary-button>
            </form>
        </div>
    </div>
</x-admin-layout>