<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.banners.index') }}" class="text-gray-400 hover:text-gray-600">Banners</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">New Banner</span>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm p-6">
            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <x-input-label value="Subtitle (small text above title)" />
                    <x-text-input name="subtitle" class="w-full mt-1" placeholder="e.g. Quran Education" />
                </div>
                <div>
                    <x-input-label value="Title (large headline)" />
                    <x-text-input name="title" class="w-full mt-1" required placeholder="e.g. MOST IMPORTANT FOR MANKIND" />
                </div>
                <div>
                    <x-input-label value="Description (line below title)" />
                    <x-text-input name="description" class="w-full mt-1" placeholder="e.g. than any education in the world" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Button Text" />
                        <x-text-input name="button_text" class="w-full mt-1" placeholder="e.g. Start Learning" />
                    </div>
                    <div>
                        <x-input-label value="Button URL" />
                        <x-text-input name="button_url" class="w-full mt-1" placeholder="/courses or https://..." />
                    </div>
                </div>
                <div>
                    <x-input-label value="Background Image" />
                    <input type="file" name="image" accept="image/*" class="w-full mt-1" required>
                    <p class="text-xs text-gray-400 mt-1">Recommended: 1920×600px. JPG or PNG.</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active" checked class="rounded">
                    <label for="is_active" class="text-sm">Show this banner immediately</label>
                </div>
                <x-primary-button>Create Banner</x-primary-button>
            </form>
        </div>
    </div>
</x-admin-layout>