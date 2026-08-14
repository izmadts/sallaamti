<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.My Counseling Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-700">{{ __('db.Upcoming') }}</h3>
                <a href="{{ route('counselor.availability.index') }}" class="text-sm text-teal-700 hover:underline">{{ __('db.Manage Availability') }} →</a>
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($upcoming as $booking)
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">{{ $booking->scheduled_at->format('d M Y, h:i A') }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->member->name }} · {{ ucfirst(str_replace('_', ' ', $booking->contact_method)) }}</p>
                        </div>
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
                        <form method="POST" action="{{ route('counselor.bookings.complete', $booking) }}" onsubmit="return promptComplete(event, this)">
                            @csrf
                            <input type="hidden" name="notes" value="">
                            <button class="text-xs text-green-600 hover:underline">{{ __('db.Mark Completed') }}</button>
                        </form>
                        <form method="POST" action="{{ route('counselor.bookings.no-show', $booking) }}">
                            @csrf
                            <button class="text-xs text-orange-600 hover:underline">{{ __('db.No-Show') }}</button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('counselor.bookings.cancel', $booking) }}" onsubmit="return confirm('{{ __('db.Cancel this session?') }}')">
                            @csrf
                            <button class="text-xs text-red-500 hover:underline">{{ __('db.Cancel') }}</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-400">{{ __('db.No upcoming sessions.') }}</p>
                @endforelse
            </div>

            <h3 class="font-semibold text-gray-700">{{ __('db.Past Sessions') }}</h3>
            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($past as $booking)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-800">{{ $booking->scheduled_at->format('d M Y, h:i A') }}</p>
                        <p class="text-sm text-gray-500">{{ $booking->member->name }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-{{ $booking->statusColor() }}-100 text-{{ $booking->statusColor() }}-700">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>
                @empty
                <p class="p-5 text-gray-400">{{ __('db.No past sessions yet.') }}</p>
                @endforelse
            </div>
            {{ $past->links() }}
        </div>
    </div>

    <script>
        function promptComplete(event, form) {
            event.preventDefault();
            const notes = prompt("{{ __('db.Any session notes to save? (optional)') }}", '');
            if (notes !== null) {
                form.notes.value = notes;
                form.submit();
            }
            return false;
        }
    </script>
</x-app-layout>
