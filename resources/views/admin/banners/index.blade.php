<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Banners</span>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-4">

        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">✅ {{ session('status') }}</div>
        @endif

        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-500">Drag to reorder. Changes save automatically.</p>
            <a href="{{ route('admin.banners.create') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">+ Add Banner</a>
        </div>

        <div id="banner-list" class="space-y-3">
            @foreach ($banners as $banner)
            <div class="bg-white rounded-lg shadow-sm p-4 flex gap-4 items-center" data-id="{{ $banner->id }}">

                {{-- Drag handle --}}
                <span class="text-gray-300 cursor-grab text-xl select-none">⠿</span>

                {{-- Thumbnail --}}
                <div class="w-24 h-16 flex-shrink-0 rounded overflow-hidden bg-gray-100">
                    <img src="{{ str_starts_with($banner->image, 'img/') ? asset($banner->image) : Storage::url($banner->image) }}"
                        class="w-full h-full object-cover">
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800 truncate">{{ $banner->title }}</p>
                    <p class="text-xs text-gray-400">{{ $banner->subtitle }} — {{ $banner->button_text }}</p>
                </div>

                {{-- Status badge --}}
                <span class="text-xs px-2 py-0.5 rounded-full {{ $banner->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $banner->is_active ? 'Active' : 'Hidden' }}
                </span>

                {{-- Actions --}}
                <div class="flex gap-2 flex-shrink-0">
                    <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}">
                        @csrf
                        <button class="text-xs {{ $banner->is_active ? 'text-orange-500' : 'text-green-600' }} hover:underline">
                            {{ $banner->is_active ? 'Hide' : 'Show' }}
                        </button>
                    </form>
                    <a href="{{ route('admin.banners.edit', $banner) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Delete this banner?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-500 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Drag-to-reorder using SortableJS from CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        const list = document.getElementById('banner-list');
        Sortable.create(list, {
            handle: 'span[class*="cursor-grab"]',
            animation: 150,
            onEnd: function() {
                const order = [...list.querySelectorAll('[data-id]')].map(el => el.dataset.id);
                fetch('{{ route('admin.banners.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order
                        })
                    });
            }
        });
    </script>
</x-admin-layout>