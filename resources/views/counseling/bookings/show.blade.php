<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.Counseling Session') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-lg font-semibold text-gray-800">{{ $booking->scheduled_at->format('l, d M Y — h:i A') }}</p>
                        <p class="text-sm text-gray-500">{{ $booking->counselor?->name ?? __('db.Counselor to be assigned') }}</p>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full bg-{{ $booking->statusColor() }}-100 text-{{ $booking->statusColor() }}-700">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <dt class="text-gray-400 text-xs">{{ __('db.Contact Method') }}</dt>
                        <dd class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $booking->contact_method)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs">{{ __('db.Concern') }}</dt>
                        <dd class="text-gray-800">{{ ucfirst($booking->supportQuery?->category ?? '') }}</dd>
                    </div>
                </dl>

                @if ($booking->supportQuery)
                <div class="border-t pt-4">
                    <p class="text-xs text-gray-400 mb-1">{{ __('db.Your Description') }}</p>
                    <p class="text-sm text-gray-700">{{ $booking->supportQuery->description }}</p>
                </div>
                @endif

                @if ($booking->notes)
                <div class="border-t pt-4 mt-4">
                    <p class="text-xs text-gray-400 mb-1">{{ __('db.Counselor Notes') }}</p>
                    <p class="text-sm text-gray-700">{{ $booking->notes }}</p>
                </div>
                @endif

                @if (!in_array($booking->status, ['completed', 'cancelled']))
                <form method="POST" action="{{ route('counseling.bookings.cancel', $booking) }}" class="mt-6" onsubmit="return confirm('{{ __('db.Cancel this session?') }}')">
                    @csrf
                    <button class="text-sm text-red-600 hover:underline">{{ __('db.Cancel Session') }}</button>
                </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
