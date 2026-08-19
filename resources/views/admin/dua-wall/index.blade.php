<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🤲 Sallaamti Wall Moderation</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @php $activeStatus = request('status', 'pending'); @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach (['pending' => $stats['pending'], 'approved' => $stats['approved'], 'hidden' => $stats['hidden'], 'all' => $stats['total']] as $status => $count)
                <a href="{{ route('admin.wall.index', ['status' => $status]) }}"
                    class="bg-white rounded-lg shadow-sm p-4 text-center border-2 {{ $activeStatus === $status ? 'border-teal-500' : 'border-transparent' }}">
                    <p class="text-2xl font-bold text-gray-800">{{ $count }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $status === 'all' ? 'Total' : ucfirst($status) }}</p>
                </a>
                @endforeach
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($duas as $dua)
                <div class="p-5 flex justify-between items-start flex-wrap gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800">
                            {{ $dua->is_anonymous ? 'Anonymous' : $dua->user->name }}
                            @if ($dua->is_anonymous)
                            <span class="text-xs text-gray-400">({{ $dua->user->name }} — hidden from public)</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-700 mt-1.5 whitespace-pre-line">{{ $dua->body }}</p>
                        @if ($dua->rejection_reason)
                        <p class="text-xs text-red-600 mt-1.5">Hidden reason: {{ $dua->rejection_reason }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1.5">Submitted {{ $dua->created_at->format('d M Y, h:i A') }}</p>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs px-2 py-1 rounded-full
                                {{ $dua->status === 'approved' ? 'bg-green-100 text-green-800' :
                                   ($dua->status === 'hidden' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($dua->status) }}
                        </span>

                        @if ($dua->status !== 'approved')
                        <form method="POST" action="{{ route('admin.wall.approve', $dua) }}">
                            @csrf
                            <button class="bg-green-600 text-white text-sm px-3 py-1.5 rounded hover:bg-green-700">Approve</button>
                        </form>
                        @endif

                        @if ($dua->status !== 'hidden')
                        <details class="text-right">
                            <summary class="cursor-pointer text-sm text-red-600 hover:text-red-700 list-none">Hide ▾</summary>
                            <form method="POST" action="{{ route('admin.wall.reject', $dua) }}" class="mt-2 flex flex-col gap-1.5 items-end">
                                @csrf
                                <input type="text" name="rejection_reason" placeholder="Reason (shown to admins only)" required
                                    class="border rounded text-xs px-2 py-1 w-48">
                                <button class="bg-red-600 text-white text-xs px-3 py-1.5 rounded hover:bg-red-700">Confirm Hide</button>
                            </form>
                        </details>
                        @endif
                        <form method="POST" action="{{ route('admin.wall.destroy', $dua) }}" onsubmit="return confirm('Permanently delete this dua? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600 hover:underline">Delete permanently</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-500">No duas here.</p>
                @endforelse
            </div>

            {{ $duas->links() }}
        </div>
    </div>
</x-admin-layout>
