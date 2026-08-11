<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Community Activities</span>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-4">

        <div class="flex justify-end">
            <a href="{{ route('admin.activity-posts.create') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">+ Add Activity</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($activityPosts as $post)
            <div class="p-4 flex gap-4 items-start">
                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                    @if ($post->photo)
                    <img src="{{ Storage::url($post->photo) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xl">🌍</div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ $post->title }}</p>
                    @if ($post->activity_date)
                    <p class="text-xs text-gray-400 mb-1">{{ $post->activity_date->format('d M Y') }}</p>
                    @endif
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $post->description }}</p>
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $post->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $post->is_active ? 'Showing' : 'Hidden' }}
                    </span>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.activity-posts.toggle', $post) }}">
                            @csrf
                            <button class="text-xs {{ $post->is_active ? 'text-orange-500' : 'text-green-600' }} hover:underline">
                                {{ $post->is_active ? 'Hide' : 'Show' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.activity-posts.edit', $post) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.activity-posts.destroy', $post) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="p-5 text-gray-400">No activities yet.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>
