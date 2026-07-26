<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Hero Banners</h2>
            <a href="{{ route('admin.banners.create') }}" class="btn-base btn-teal text-sm px-4 py-2">+ Add Banner</a>
        </div>
    </x-slot>
    <div class="max-w-4xl space-y-4">
        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-xl text-sm">✅ {{ session('status') }}</div>
        @endif
        @forelse ($banners as $banner)
        <div class="bg-white rounded-xl shadow-sm p-4 flex gap-4 items-center">
            <img src="{{ Storage::url($banner->image) }}" class="w-24 h-16 object-cover rounded-lg flex-shrink-0">
            <div class="flex-1">
                <p class="font-semibold text-gray-800">{{ $banner->title }}</p>
                <p class="text-xs text-gray-400">{{ $banner->subtitle }}</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}">
                    @csrf
                    <button class="text-xs px-2 py-1 rounded-full font-semibold {{ $banner->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $banner->is_active ? '✅ Active' : '⭕ Inactive' }}
                    </button>
                </form>
                <a href="{{ route('admin.banners.edit', $banner) }}" class="text-xs px-3 py-1 border border-gray-200 rounded text-gray-600 hover:bg-gray-50">Edit</a>
                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="text-xs px-3 py-1 border border-red-200 rounded text-red-500 hover:bg-red-50">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl p-10 text-center text-gray-400">
            <div class="text-4xl mb-2">🖼️</div>
            No banners yet. Add one to show on the homepage carousel.
        </div>
        @endforelse
    </div>
</x-admin-layout>