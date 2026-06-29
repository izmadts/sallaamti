<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Volunteer Applications</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($volunteers as $volunteer)
                <div class="p-5 flex justify-between items-start flex-wrap gap-3">
                    <div>
                        <p class="font-medium text-gray-800">{{ $volunteer->name }}</p>
                        <p class="text-sm text-gray-500">{{ $volunteer->email }} — {{ $volunteer->phone }}</p>
                        <p class="text-sm text-gray-500">{{ $volunteer->city ?: 'City not provided' }} | Interest: {{ $volunteer->area_of_interest ?: 'Not specified' }}</p>
                        @if ($volunteer->message)
                        <p class="text-sm text-gray-600 mt-2 italic">"{{ $volunteer->message }}"</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Applied {{ $volunteer->created_at->format('d M Y, h:i A') }}</p>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs px-2 py-1 rounded-full
                                {{ $volunteer->status === 'approved' ? 'bg-green-100 text-green-800' :
                                   ($volunteer->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($volunteer->status) }}
                        </span>

                        @if ($volunteer->status === 'pending')
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.volunteers.approve', $volunteer) }}">
                                @csrf
                                <button class="bg-green-600 text-white text-sm px-3 py-1.5 rounded hover:bg-green-700">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.volunteers.reject', $volunteer) }}">
                                @csrf
                                <button class="bg-red-600 text-white text-sm px-3 py-1.5 rounded hover:bg-red-700">Reject</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-500">No volunteer applications yet.</p>
                @endforelse
            </div>

            {{ $volunteers->links() }}
        </div>
    </div>
</x-app-layout>