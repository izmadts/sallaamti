<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.My Counseling Sessions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="flex justify-end">
                <a href="{{ route('counseling.book.start') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">+ {{ __('db.Book a Session') }}</a>
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($bookings as $booking)
                <a href="{{ route('counseling.bookings.show', $booking) }}" class="block p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-800">{{ $booking->scheduled_at->format('d M Y, h:i A') }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->counselor?->name ?? __('db.Counselor to be assigned') }} · {{ ucfirst(str_replace('_', ' ', $booking->contact_method)) }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-{{ $booking->statusColor() }}-100 text-{{ $booking->statusColor() }}-700">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </div>
                </a>
                @empty
                <p class="p-5 text-gray-400">{{ __("db.You haven't booked any counseling sessions yet.") }}</p>
                @endforelse
            </div>

            {{ $bookings->links() }}
        </div>
    </div>
</x-app-layout>
