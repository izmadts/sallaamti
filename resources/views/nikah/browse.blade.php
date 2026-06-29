<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Browse Matches</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            <form method="GET" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs text-gray-500">City</label>
                    <input type="text" name="city" value="{{ request('city') }}" class="border-gray-300 rounded text-sm block">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Min Age</label>
                    <input type="number" name="min_age" value="{{ request('min_age') }}" class="border-gray-300 rounded text-sm block w-20">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Max Age</label>
                    <input type="number" name="max_age" value="{{ request('max_age') }}" class="border-gray-300 rounded text-sm block w-20">
                </div>
                <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded">Filter</button>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($profiles as $profile)
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <div class="text-4xl mb-2">👤</div>
                    <h4 class="font-semibold text-gray-800">{{ $profile->age }} yrs, {{ $profile->city }}</h4>
                    <p class="text-sm text-gray-500">{{ $profile->profession ?: 'Profession not listed' }}</p>
                    <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $profile->marital_status)) }}</p>

                    @if (in_array($profile->id, $sentInterestIds))
                    <button disabled class="mt-3 w-full bg-gray-200 text-gray-500 text-sm py-2 rounded">Interest Sent</button>
                    @else
                    <form method="POST" action="{{ route('nikah.interest.send', $profile) }}">
                        @csrf
                        <button class="mt-3 w-full bg-pink-600 text-white text-sm py-2 rounded hover:bg-pink-700">Express Interest</button>
                    </form>
                    @endif
                </div>
                @empty
                <p class="text-gray-500 col-span-full">No matches found yet. Try adjusting filters.</p>
                @endforelse
            </div>

            {{ $profiles->links() }}
        </div>
    </div>
</x-app-layout>