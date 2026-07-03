<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Testimonials</span>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-4">
        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">✅ {{ session('status') }}</div>
        @endif

        <div class="flex justify-end">
            <a href="{{ route('admin.testimonials.create') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">+ Add Testimonial</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($testimonials as $t)
            <div class="p-4 flex gap-4 items-start">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 flex-shrink-0">
                    @if ($t->photo)
                    <img src="{{ Storage::url($t->photo) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-lg">👤</div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ $t->name }} <span class="text-gray-400 text-xs font-normal">— {{ $t->location }}</span></p>
                    <p class="text-xs text-yellow-500 mb-1">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</p>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $t->content }}</p>
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $t->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $t->is_active ? 'Showing' : 'Hidden' }}
                    </span>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.testimonials.toggle', $t) }}">
                            @csrf
                            <button class="text-xs {{ $t->is_active ? 'text-orange-500' : 'text-green-600' }} hover:underline">
                                {{ $t->is_active ? 'Hide' : 'Show' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.testimonials.edit', $t) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="p-5 text-gray-400">No testimonials yet.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>