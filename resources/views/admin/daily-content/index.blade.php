<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Daily Ayah / Hadith</span>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-4">

        <p class="text-sm text-gray-500">One of these shows on the dashboard and homepage each day, in rotation — same one for everyone, every day. Only active entries are used.</p>

        <div class="flex justify-end">
            <a href="{{ route('admin.daily-content.create') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">+ Add Entry</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($dailyContents as $content)
            <div class="p-4 flex gap-4 items-start">
                <div class="flex-1">
                    <p class="font-medium text-gray-800">
                        <span class="text-xs uppercase tracking-wide {{ $content->type === 'ayah' ? 'text-teal-600' : 'text-amber-600' }}">{{ $content->type }}</span>
                        — {{ $content->reference }}
                    </p>
                    @if ($content->arabic_text)
                    <p class="text-gray-700 mt-1" dir="rtl">{{ $content->arabic_text }}</p>
                    @endif
                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit(strip_tags($content->translation), 140) }}</p>
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $content->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $content->is_active ? 'Active' : 'Hidden' }}
                    </span>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.daily-content.toggle', $content) }}">
                            @csrf
                            <button class="text-xs {{ $content->is_active ? 'text-orange-500' : 'text-green-600' }} hover:underline">
                                {{ $content->is_active ? 'Hide' : 'Show' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.daily-content.edit', $content) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.daily-content.destroy', $content) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="p-5 text-gray-400">No entries yet — the dashboard widget stays hidden until at least one is added.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>
