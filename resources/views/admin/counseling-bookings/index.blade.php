<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Counseling Bookings</h2>
    </x-slot>
    <div class="space-y-6 max-w-6xl">

        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
        @endif
        @if (session('error'))
        <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
            <div>
                <label class="text-xs font-semibold text-gray-500 block mb-1">Status</label>
                <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                    <option value="">All</option>
                    @foreach (['requested', 'confirmed', 'completed', 'cancelled', 'no_show'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 block mb-1">Counselor</label>
                <select name="counselor_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                    <option value="">All</option>
                    <option value="unassigned" {{ request('counselor_id') === 'unassigned' ? 'selected' : '' }}>⚠️ Unassigned</option>
                    @foreach ($counselors as $c)
                    <option value="{{ $c->id }}" {{ (string) request('counselor_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600 pb-2">
                <input type="checkbox" name="urgent_only" value="1" {{ request()->boolean('urgent_only') ? 'checked' : '' }} class="rounded text-red-600">
                🚨 Urgent only
            </label>
            <button class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">Filter</button>
            @if (request()->hasAny(['status', 'counselor_id', 'urgent_only']))
            <a href="{{ route('admin.counseling-bookings.index') }}" class="text-sm text-gray-400 hover:text-gray-600">✕ Clear</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Counselor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Scheduled</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Rating</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $booking->member->name }}
                                @if ($booking->isUrgent())
                                <span class="text-red-600" title="Urgent / safety concern">🚨</span>
                                @endif
                                @if ($booking->isAnonymous())
                                <span class="text-xs text-gray-400" title="Anonymous to counselor">🎭</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                @if (!$booking->counselor)
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-orange-100 text-orange-700">Unassigned</span>
                                @else
                                {{ $booking->counselor->name }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $booking->scheduled_at->format('d M Y, h:i A') }}
                                @if ($booking->status === 'requested' && !$booking->confirmed_at && $booking->created_at->lt(now()->subDay()))
                                <br><span class="text-xs text-red-500">⏳ Waiting {{ $booking->created_at->diffForHumans(null, true) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-{{ $booking->statusColor() }}-100 text-{{ $booking->statusColor() }}-700">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-amber-500">
                                {{ $booking->member_rating ? str_repeat('★', $booking->member_rating) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.counseling-bookings.show', $booking) }}" class="text-xs px-3 py-1 rounded border border-gray-200 text-gray-600 hover:bg-gray-50">
                                    Open →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                <div class="text-3xl mb-2">🤝</div>
                                No bookings found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-100">{{ $bookings->links() }}</div>
        </div>
    </div>
</x-admin-layout>
