<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Counseling Booking #{{ $booking->id }}</h2>
    </x-slot>
    <div class="max-w-3xl space-y-6">

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="font-semibold text-gray-800">{{ $booking->scheduled_at->format('l, d M Y — h:i A') }}</p>
                    <p class="text-sm text-gray-500">{{ $booking->member->name }} ({{ $booking->member->email ?? $booking->member->phone }})</p>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-{{ $booking->statusColor() }}-100 text-{{ $booking->statusColor() }}-700">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>

            <dl class="grid grid-cols-2 gap-4 text-sm mb-4">
                <div>
                    <dt class="text-gray-400 text-xs">Contact Method</dt>
                    <dd class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $booking->contact_method)) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs">Concern</dt>
                    <dd class="text-gray-800">{{ ucfirst($booking->supportQuery?->category ?? '') }}</dd>
                </div>
            </dl>

            @if ($booking->supportQuery)
            <div class="border-t pt-4">
                <p class="text-xs text-gray-400 mb-1">Subject</p>
                <p class="text-sm text-gray-800 font-medium mb-2">{{ $booking->supportQuery->subject }}</p>
                <p class="text-xs text-gray-400 mb-1">Description</p>
                <p class="text-sm text-gray-700">{{ $booking->supportQuery->description }}</p>
            </div>
            @endif

            @if ($booking->notes)
            <div class="border-t pt-4 mt-4">
                <p class="text-xs text-gray-400 mb-1">Counselor Notes</p>
                <p class="text-sm text-gray-700">{{ $booking->notes }}</p>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-3">Reassign Counselor</h3>
            <form method="POST" action="{{ route('admin.counseling-bookings.reassign', $booking) }}" class="flex gap-3 items-end">
                @csrf
                <select name="counselor_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm flex-1">
                    @foreach ($counselors as $c)
                    <option value="{{ $c->id }}" {{ $booking->counselor_id === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">Reassign</button>
            </form>
        </div>

        @if (!in_array($booking->status, ['completed', 'cancelled']))
        <form method="POST" action="{{ route('admin.counseling-bookings.cancel', $booking) }}" onsubmit="return confirm('Cancel this booking?')">
            @csrf
            <button class="text-sm text-red-600 hover:underline">Cancel Booking</button>
        </form>
        @endif

    </div>
</x-admin-layout>
