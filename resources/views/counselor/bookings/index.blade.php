<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.My Counseling Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-700">{{ __('db.Upcoming') }}</h3>
                <a href="{{ route('counselor.availability.index') }}" class="text-sm text-teal-700 hover:underline">{{ __('db.Manage Availability') }} →</a>
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($upcoming as $booking)
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <a href="{{ route('counselor.bookings.show', $booking) }}" class="hover:opacity-75">
                            <p class="font-medium text-gray-800">{{ $booking->scheduled_at->format('d M Y, h:i A') }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $booking->isAnonymous() ? '🎭 ' . __('db.Anonymous member') : $booking->member->name }} · {{ ucfirst(str_replace('_', ' ', $booking->contact_method)) }}
                                @if ($booking->isUrgent())
                                <span class="text-red-600 font-semibold">· 🚨 {{ __('db.Urgent') }}</span>
                                @endif
                            </p>
                            @if ($booking->supportQuery)
                            <p class="text-xs text-gray-400 mt-0.5">{{ ucfirst($booking->supportQuery->category) }} — {{ $booking->supportQuery->subject }}</p>
                            @endif
                        </a>
                        <span class="text-xs px-2 py-1 rounded-full bg-{{ $booking->statusColor() }}-100 text-{{ $booking->statusColor() }}-700">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </div>
                    <div class="flex gap-3 mt-3">
                        @if ($booking->status === 'requested')
                        <form method="POST" action="{{ route('counselor.bookings.confirm', $booking) }}">
                            @csrf
                            <button class="text-xs text-blue-600 hover:underline">{{ __('db.Confirm') }}</button>
                        </form>
                        @endif
                        @if ($booking->status === 'confirmed')
                        <a href="{{ route('counselor.bookings.show', $booking) }}" class="text-xs text-green-600 hover:underline">{{ __('db.Mark Completed') }} →</a>
                        <form method="POST" action="{{ route('counselor.bookings.no-show', $booking) }}">
                            @csrf
                            <button class="text-xs text-orange-600 hover:underline">{{ __('db.No-Show') }}</button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('counselor.bookings.cancel', $booking) }}" onsubmit="return confirm('{{ __('db.Cancel this session?') }}')">
                            @csrf
                            <button class="text-xs text-red-500 hover:underline">{{ __('db.Cancel') }}</button>
                        </form>
                        <a href="{{ route('counselor.bookings.show', $booking) }}" class="text-xs text-gray-500 hover:underline ml-auto">{{ __('db.View Details') }} →</a>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-400">{{ __('db.No upcoming sessions.') }}</p>
                @endforelse
            </div>

            <h3 class="font-semibold text-gray-700">{{ __('db.Past Sessions') }}</h3>
            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($past as $booking)
                <a href="{{ route('counselor.bookings.show', $booking) }}" class="p-4 flex justify-between items-center hover:bg-gray-50">
                    <div>
                        <p class="font-medium text-gray-800">{{ $booking->scheduled_at->format('d M Y, h:i A') }}</p>
                        <p class="text-sm text-gray-500">{{ $booking->isAnonymous() ? '🎭 ' . __('db.Anonymous member') : $booking->member->name }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-{{ $booking->statusColor() }}-100 text-{{ $booking->statusColor() }}-700">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </a>
                @empty
                <p class="p-5 text-gray-400">{{ __('db.No past sessions yet.') }}</p>
                @endforelse
            </div>
            {{ $past->links() }}
        </div>
    </div>
</x-app-layout>
