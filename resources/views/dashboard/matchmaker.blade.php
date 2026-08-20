<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Matchmaker Panel — {{ $user->name }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-xl p-4 flex items-start gap-3" style="background: var(--teal-light); border: 1px solid #0d6b6b33">
                <span class="text-xl">🛡️</span>
                <p class="text-sm text-gray-700">
                    You can browse every profile, but contact details, CNIC, and photos stay hidden — request contact from admin when you find a good match.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Active Profiles</p>
                    <p class="text-3xl font-bold text-teal-700 mt-1">{{ $stats['total_profiles'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Your Pending Requests</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending_requests'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Approved for You</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['approved_requests'] }}</p>
                </div>
            </div>

            <div>
                <a href="{{ route('matchmaker.nikah.index') }}" class="inline-block text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90" style="background: #0d6b6b">
                    Browse Nikah Profiles →
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
