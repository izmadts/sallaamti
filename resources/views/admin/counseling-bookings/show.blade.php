<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Counseling Booking #{{ $booking->id }}</h2>
    </x-slot>
    <div class="max-w-3xl space-y-6">

        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
        @endif
        @if (session('error'))
        <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="font-semibold text-gray-800">{{ $booking->scheduled_at->format('l, d M Y — h:i A') }}</p>
                    <p class="text-sm text-gray-500">{{ $booking->member->name }} ({{ $booking->member->email ?? $booking->member->phone }})</p>
                    @if ($booking->isAnonymous())
                    <span class="text-xs text-gray-400" title="Kept anonymous to the counselor">🎭 Anonymous to counselor</span>
                    @endif
                    @if ($booking->isUrgent())
                    <span class="text-xs font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full inline-block mt-1">🚨 Urgent / safety concern</span>
                    @endif
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
                @if ($booking->member_rating)
                <div>
                    <dt class="text-gray-400 text-xs">Member Rating</dt>
                    <dd class="text-amber-500">{{ str_repeat('★', $booking->member_rating) }}{{ str_repeat('☆', 5 - $booking->member_rating) }}</dd>
                </div>
                @endif
            </dl>

            @if ($booking->supportQuery)
            <div class="border-t pt-4">
                <p class="text-xs text-gray-400 mb-1">Subject</p>
                <p class="text-sm text-gray-800 font-medium mb-2">{{ $booking->supportQuery->subject }}</p>
                <p class="text-xs text-gray-400 mb-1">Description</p>
                <p class="text-sm text-gray-700">{{ $booking->supportQuery->description }}</p>
            </div>
            @endif

            @if ($booking->meeting_link)
            <div class="border-t pt-4 mt-4">
                <p class="text-xs text-gray-400 mb-1">Meeting Link</p>
                <a href="{{ $booking->meeting_link }}" target="_blank" class="text-sm text-teal-600 hover:underline break-all">{{ $booking->meeting_link }}</a>
            </div>
            @endif

            @if ($booking->notes)
            <div class="border-t pt-4 mt-4">
                <p class="text-xs text-gray-400 mb-1">Notes shared with member</p>
                <p class="text-sm text-gray-700">{{ $booking->notes }}</p>
            </div>
            @endif

            @if ($booking->internal_notes)
            <div class="border-t pt-4 mt-4 bg-amber-50 -mx-6 -mb-6 px-6 pb-6 rounded-b-xl">
                <p class="text-xs text-amber-700 font-semibold mb-1">🔒 Internal notes (never shown to member)</p>
                <p class="text-sm text-amber-900">{{ $booking->internal_notes }}</p>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-3">Reassign Counselor</h3>
            <form method="POST" action="{{ route('admin.counseling-bookings.reassign', $booking) }}" class="flex gap-3 items-end">
                @csrf
                <select name="counselor_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm flex-1">
                    @foreach ($counselors as $c)
                    <option value="{{ $c->id }}" {{ $booking->counselor_id === $c->id ? 'selected' : '' }}>{{ $c->name }}{{ $c->hasRole('admin') && !$c->hasRole('counselor') ? ' (admin)' : '' }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">Reassign</button>
            </form>
            <form method="POST" action="{{ route('admin.counseling-bookings.reassign', $booking) }}" class="mt-2">
                @csrf
                <input type="hidden" name="counselor_id" value="{{ auth()->id() }}">
                <button class="text-xs text-teal-700 hover:underline">Assign to me →</button>
            </form>
        </div>

        @if (!in_array($booking->status, ['completed', 'cancelled']))
        <form method="POST" action="{{ route('admin.counseling-bookings.cancel', $booking) }}" onsubmit="return confirm('Cancel this booking?')">
            @csrf
            <button class="text-sm text-red-600 hover:underline">Cancel Booking</button>
        </form>
        @endif

        {{-- Message thread --}}
        @if ($booking->supportQuery)
        <h3 class="font-semibold text-gray-700 pt-2">Messages</h3>
        @foreach ($booking->supportQuery->responses->where('is_internal', false) as $response)
        <div class="flex {{ $response->responder_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-lg rounded-2xl p-4 text-sm {{ $response->responder_id === auth()->id() ? 'bg-teal-600 text-white' : 'bg-white text-gray-800 shadow-sm' }}">
                <p class="font-semibold text-xs mb-1 {{ $response->responder_id === auth()->id() ? 'text-teal-100' : 'text-gray-400' }}">
                    {{ $response->responder_id === auth()->id() ? 'You' : ($response->responder->name ?? ($booking->isAnonymous() ? 'Member' : $booking->member->name)) }}
                </p>
                <p class="leading-relaxed">{{ $response->message }}</p>
                <p class="text-xs mt-2 {{ $response->responder_id === auth()->id() ? 'text-teal-200' : 'text-gray-400' }}">{{ $response->created_at->format('d M, h:i A') }}</p>
            </div>
        </div>
        @endforeach

        <div class="bg-white rounded-2xl shadow-sm p-5">
            <form method="POST" action="{{ route('admin.counseling-bookings.reply', $booking) }}">
                @csrf
                <textarea name="message" rows="3" required maxlength="2000"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-teal-500 resize-none mb-3"
                    placeholder="Write a message..."></textarea>
                <button class="px-6 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">Send</button>
            </form>
        </div>
        @endif

    </div>
</x-admin-layout>
