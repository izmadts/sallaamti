<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Team</span>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-4">

        <div class="flex justify-end">
            <a href="{{ route('admin.team-members.create') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">+ Add Team Member</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($teamMembers as $member)
            <div class="p-4 flex gap-4 items-start">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 flex-shrink-0">
                    @if ($member->photo)
                    <img src="{{ Storage::url($member->photo) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-lg">👤</div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">
                        {{ $member->name }} <span class="text-gray-400 text-xs font-normal">— {{ $member->role }}</span>
                        @if ($member->is_founder)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 ml-1">Founder</span>
                        @endif
                    </p>
                    @if ($member->bio)
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $member->bio }}</p>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $member->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $member->is_active ? 'Showing' : 'Hidden' }}
                    </span>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.team-members.toggle', $member) }}">
                            @csrf
                            <button class="text-xs {{ $member->is_active ? 'text-orange-500' : 'text-green-600' }} hover:underline">
                                {{ $member->is_active ? 'Hide' : 'Show' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.team-members.edit', $member) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.team-members.destroy', $member) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="p-5 text-gray-400">No team members yet.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>
