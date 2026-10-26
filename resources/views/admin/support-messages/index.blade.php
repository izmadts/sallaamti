<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">💬 Member Messages</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($threads as $thread)
                @php $last = $thread->adminMessages->first(); @endphp
                <a href="{{ route('admin.support-messages.show', $thread) }}" class="p-5 flex justify-between items-start gap-3 hover:bg-gray-50">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800">{{ $thread->name }}</p>
                        <p class="text-sm text-gray-500 mt-0.5 line-clamp-1">{{ $last?->body }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <span class="text-xs text-gray-400">{{ $last?->created_at->diffForHumans() }}</span>
                        @if ($thread->unread_count > 0)
                        <span class="bg-teal-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $thread->unread_count }}</span>
                        @endif
                    </div>
                </a>
                @empty
                <p class="p-5 text-gray-500">No member messages yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
