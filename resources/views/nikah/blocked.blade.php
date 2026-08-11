<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Blocked Profiles</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @forelse ($blocks as $block)
            <div class="bg-white shadow-sm rounded-lg p-4 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-800">{{ $block->blocked->user->name ?? 'Deleted profile' }}</p>
                    <p class="text-xs text-gray-400">Blocked {{ $block->created_at->diffForHumans() }}</p>
                </div>
                <form method="POST" action="{{ route('nikah.unblock', $block) }}"
                    onsubmit="return confirm('Unblock this profile? They will be able to see and contact you again.')">
                    @csrf
                    @method('DELETE')
                    <button class="bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded hover:bg-gray-200">Unblock</button>
                </form>
            </div>
            @empty
            <p class="text-gray-500">You haven't blocked anyone.</p>
            @endforelse

        </div>
    </div>
</x-app-layout>
