<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📝 Public Posts Moderation</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            @php $activeStatus = $status ?? 'pending'; @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach (['pending' => $stats['pending'], 'published' => $stats['published'], 'rejected' => $stats['rejected'], 'all' => $stats['total']] as $s => $count)
                <a href="{{ route('admin.posts.index', ['status' => $s]) }}"
                    class="bg-white rounded-lg shadow-sm p-4 text-center border-2 {{ $activeStatus === $s ? 'border-teal-500' : 'border-transparent' }}">
                    <p class="text-2xl font-bold text-gray-800">{{ $count }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $s === 'all' ? 'Total' : ucfirst($s) }}</p>
                </a>
                @endforeach
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($posts as $post)
                <div class="p-5 flex justify-between items-start flex-wrap gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800">{{ $post->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">by {{ $post->author->name }} — {{ $post->created_at->format('d M Y, h:i A') }}</p>
                        <p class="text-sm text-gray-600 mt-1.5 line-clamp-2">{{ $post->excerpt }}</p>
                        @if ($post->rejection_reason)
                        <p class="text-xs text-red-600 mt-1.5">Rejected: {{ $post->rejection_reason }}</p>
                        @endif
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs px-2 py-1 rounded-full
                                {{ $post->status === 'published' ? 'bg-green-100 text-green-800' :
                                   ($post->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($post->status) }}
                        </span>

                        @if ($post->status === 'published')
                        <a href="{{ route('posts.show', $post) }}" target="_blank" class="text-xs text-teal-700 hover:underline">View live</a>
                        @endif

                        @if ($post->status !== 'published')
                        <form method="POST" action="{{ route('admin.posts.approve', $post) }}">
                            @csrf
                            <button class="bg-green-600 text-white text-sm px-3 py-1.5 rounded hover:bg-green-700">Approve</button>
                        </form>
                        @endif

                        @if ($post->status !== 'rejected')
                        <details class="text-right">
                            <summary class="cursor-pointer text-sm text-red-600 hover:text-red-700 list-none">Reject ▾</summary>
                            <form method="POST" action="{{ route('admin.posts.reject', $post) }}" class="mt-2 flex flex-col gap-1.5 items-end">
                                @csrf
                                <input type="text" name="rejection_reason" placeholder="Reason (shown to the author)" required
                                    class="border rounded text-xs px-2 py-1 w-52">
                                <button class="bg-red-600 text-white text-xs px-3 py-1.5 rounded hover:bg-red-700">Confirm Reject</button>
                            </form>
                        </details>
                        @endif

                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Permanently delete this post? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600 hover:underline">Delete permanently</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-500">No posts here.</p>
                @endforelse
            </div>

            {{ $posts->links() }}
        </div>
    </div>
</x-admin-layout>
