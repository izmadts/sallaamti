<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Saved Profiles</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($saved as $item)
            <div class="bg-white rounded-lg shadow-sm p-5 flex justify-between items-center">
                <div>
                    <p class="font-medium">{{ $item->savedProfile->age }} yrs, {{ $item->savedProfile->city }}</p>
                    <p class="text-sm text-gray-500">{{ $item->savedProfile->profession ?: 'Profession not listed' }}</p>
                </div>
                <div class="flex gap-2">
                    @if (in_array($item->saved_profile_id, $sentInterestIds))
                    <button disabled class="bg-gray-200 text-gray-500 text-sm px-3 py-1.5 rounded">Sent ✓</button>
                    @else
                    <form method="POST" action="{{ route('nikah.interest.send', $item->savedProfile) }}">
                        @csrf
                        <button class="bg-pink-600 text-white text-sm px-3 py-1.5 rounded">Express Interest</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('nikah.save', $item->savedProfile) }}">
                        @csrf
                        <button class="text-sm border border-gray-200 px-3 py-1.5 rounded text-red-500">Remove</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-gray-500">No saved profiles yet. Browse matches and click ☆ to save profiles.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>