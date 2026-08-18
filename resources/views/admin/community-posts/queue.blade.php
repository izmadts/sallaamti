<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.community-posts.index') }}" class="text-gray-400 hover:text-gray-600">Community Posts</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Queue</span>
        </div>
    </x-slot>

    <div class="max-w-3xl space-y-4">

        <div class="bg-white rounded-lg shadow-sm p-4 flex items-center justify-between flex-wrap gap-2">
            <p class="text-sm text-gray-600">
                📅 <strong>{{ $batchSize }}</strong> {{ Str::plural('post', $batchSize) }} auto-publish daily at <strong>{{ $batchTime }}</strong> from the top of this list — <strong>{{ $posts->count() }}</strong> currently queued.
            </p>
            <a href="{{ route('admin.integrations.index') }}" class="text-xs text-[--teal] hover:underline flex-shrink-0">Change batch size/time →</a>
        </div>

        <p class="text-xs text-gray-400">Drag to reorder — items nearer the top go out first.</p>

        <div id="queue-list" class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($posts as $post)
            <div class="p-4 flex gap-3 items-center" draggable="true" data-id="{{ $post->id }}">
                <span class="cursor-grab text-gray-300 select-none" title="Drag to reorder">⠿</span>
                <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                    @if ($post->video)
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xl">🎬</div>
                    @elseif ($post->photo)
                    <img src="{{ Storage::url($post->photo) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xl">📣</div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800 truncate">{{ $post->title }}</p>
                    @if (!empty($post->tags))
                    <div class="flex flex-wrap gap-1 mt-0.5">
                        @foreach ($post->tags as $tag)
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">#{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('admin.community-posts.edit', $post) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                    <form method="POST" action="{{ route('admin.community-posts.publish-now', $post) }}">
                        @csrf
                        <button class="text-xs text-green-600 hover:underline">Post Now</button>
                    </form>
                    <form method="POST" action="{{ route('admin.community-posts.destroy', $post) }}" onsubmit="return confirm('Remove from queue?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-500 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="p-5 text-gray-400">Nothing queued. <a href="{{ route('admin.community-posts.bulk-upload') }}" class="text-[--teal] hover:underline">Bulk upload some photos/videos</a> to get started.</p>
            @endforelse
        </div>
    </div>

    <script>
        (function () {
            const list = document.getElementById('queue-list');
            if (!list) return;
            let dragging = null;

            list.addEventListener('dragstart', (e) => {
                const row = e.target.closest('[data-id]');
                if (!row) return;
                dragging = row;
                row.classList.add('opacity-40');
            });

            list.addEventListener('dragend', () => {
                dragging?.classList.remove('opacity-40');
                dragging = null;
                persistOrder();
            });

            list.addEventListener('dragover', (e) => {
                e.preventDefault();
                const target = e.target.closest('[data-id]');
                if (!target || target === dragging || !dragging) return;

                const rect = target.getBoundingClientRect();
                const before = (e.clientY - rect.top) < rect.height / 2;
                list.insertBefore(dragging, before ? target : target.nextSibling);
            });

            async function persistOrder() {
                const ids = Array.from(list.querySelectorAll('[data-id]')).map((row) => row.dataset.id);
                try {
                    await fetch('{{ route('admin.community-posts.queue.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ ids }),
                    });
                } catch (e) {
                    // worst case the drag just didn't persist — reload restores true order
                }
            }
        })();
    </script>
</x-admin-layout>
