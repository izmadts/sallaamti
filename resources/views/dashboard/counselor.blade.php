<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('db.Counselor Panel') }} — {{ $user->name }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('db.Pending Requests') }}</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending_requests'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('db.Upcoming Sessions') }}</p>
                    <p class="text-3xl font-bold text-teal-700 mt-1">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('db.Sessions Completed') }}</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['completed_total'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('db.Avg. Rating') }}</p>
                    <p class="text-3xl font-bold text-amber-500 mt-1">{{ $stats['avg_rating'] ?: '—' }} @if($stats['avg_rating']) <span class="text-lg">★</span>@endif</p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('counselor.bookings.index') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">{{ __('db.View All Bookings') }}</a>
                <a href="{{ route('counselor.availability.index') }}" class="border border-teal-700 text-teal-700 text-sm px-4 py-2 rounded hover:bg-teal-50">{{ __('db.Manage Availability') }}</a>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.Next Up') }}</h3>
                <div class="divide-y">
                    @forelse ($nextBookings as $booking)
                    <a href="{{ route('counselor.bookings.show', $booking) }}" class="py-3 flex justify-between items-center hover:bg-gray-50 -mx-2 px-2 rounded">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $booking->scheduled_at->format('d M Y, h:i A') }} @if($booking->isUrgent()) <span class="text-red-600">🚨</span>@endif</p>
                            <p class="text-xs text-gray-500">{{ $booking->isAnonymous() ? '🎭 Anonymous member' : $booking->member->name }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-{{ $booking->statusColor() }}-100 text-{{ $booking->statusColor() }}-700">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </a>
                    @empty
                    <p class="text-sm text-gray-400 py-3">{{ __('db.No upcoming sessions.') }}</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
