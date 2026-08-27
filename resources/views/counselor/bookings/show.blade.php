<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('counselor.bookings.index') }}" class="text-gray-400 hover:text-gray-600">{{ __('db.My Counseling Bookings') }}</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ $booking->scheduled_at->format('d M Y, h:i A') }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="font-semibold text-gray-800 text-lg">{{ $booking->isAnonymous() ? '🎭 ' . __('db.Anonymous member') : $booking->member->name }}</p>
                        <p class="text-sm text-gray-500">{{ $booking->scheduled_at->format('l, d M Y — h:i A') }} · {{ ucfirst(str_replace('_', ' ', $booking->contact_method)) }}</p>
                        @if ($booking->isUrgent())
                        <span class="text-xs font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full inline-block mt-1">🚨 {{ __('db.Urgent / safety concern') }}</span>
                        @endif
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-{{ $booking->statusColor() }}-100 text-{{ $booking->statusColor() }}-700">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>

                @if ($booking->supportQuery)
                <div class="border-t pt-4">
                    <p class="text-xs text-gray-400 mb-1">{{ __('db.Concern') }} — {{ ucfirst($booking->supportQuery->category) }}</p>
                    <p class="text-sm text-gray-800 font-medium mb-2">{{ $booking->supportQuery->subject }}</p>
                    <p class="text-sm text-gray-700">{{ $booking->supportQuery->description }}</p>
                </div>
                @endif

                @if ($booking->meeting_link)
                <div class="border-t pt-4 mt-4">
                    <p class="text-xs text-gray-400 mb-1">{{ __('db.Meeting Link') }}</p>
                    <a href="{{ $booking->meeting_link }}" target="_blank" class="text-sm text-teal-600 hover:underline break-all">{{ $booking->meeting_link }}</a>
                </div>
                @endif

                @if ($booking->notes)
                <div class="border-t pt-4 mt-4">
                    <p class="text-xs text-gray-400 mb-1">{{ __('db.Notes shared with member') }}</p>
                    <p class="text-sm text-gray-700">{{ $booking->notes }}</p>
                </div>
                @endif

                @if ($booking->internal_notes)
                <div class="border-t pt-4 mt-4 bg-amber-50 -mx-6 -mb-6 px-6 pb-6 rounded-b-2xl">
                    <p class="text-xs text-amber-700 font-semibold mb-1">🔒 {{ __('db.Internal notes (never shown to member)') }}</p>
                    <p class="text-sm text-amber-900">{{ $booking->internal_notes }}</p>
                </div>
                @endif
            </div>

            {{-- Actions --}}
            @if ($booking->status === 'requested')
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.Confirm This Session') }}</h3>
                <form method="POST" action="{{ route('counselor.bookings.confirm', $booking) }}" class="space-y-3">
                    @csrf
                    @if ($booking->contact_method === 'video')
                    <div>
                        <x-input-label for="meeting_link" :value="__('db.Meeting link (Zoom, Google Meet, WhatsApp Video, etc. — optional, can add later)')" />
                        <x-text-input id="meeting_link" name="meeting_link" class="w-full mt-1" placeholder="https://..." />
                    </div>
                    @endif
                    <x-primary-button>{{ __('db.Confirm Session') }}</x-primary-button>
                </form>
            </div>
            @endif

            @if ($booking->status === 'confirmed')
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.Mark Session Completed') }}</h3>
                <form method="POST" action="{{ route('counselor.bookings.complete', $booking) }}" class="space-y-3">
                    @csrf
                    <div>
                        <x-input-label for="notes" :value="__('db.Notes for the member (they will see this)')" />
                        <textarea id="notes" name="notes" rows="3" maxlength="2000" class="w-full mt-1 border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <x-input-label for="internal_notes" :value="__('db.Internal case notes (never shown to member)')" />
                        <textarea id="internal_notes" name="internal_notes" rows="3" maxlength="2000" class="w-full mt-1 border-gray-300 rounded-md shadow-sm bg-amber-50"></textarea>
                    </div>
                    <x-primary-button>{{ __('db.Mark Completed') }}</x-primary-button>
                </form>
            </div>
            @endif

            @if (in_array($booking->status, ['requested', 'confirmed']))
            <div class="flex gap-4">
                @if ($booking->status === 'confirmed')
                <form method="POST" action="{{ route('counselor.bookings.no-show', $booking) }}">
                    @csrf
                    <button class="text-sm text-orange-600 hover:underline">{{ __('db.Mark No-Show') }}</button>
                </form>
                @endif
                <form method="POST" action="{{ route('counselor.bookings.cancel', $booking) }}" onsubmit="return confirm('{{ __('db.Cancel this session?') }}')">
                    @csrf
                    <button class="text-sm text-red-500 hover:underline">{{ __('db.Cancel Session') }}</button>
                </form>
            </div>
            @endif

            {{-- Conversation thread — reuses the same SupportQuery/QueryResponse
                 messaging the /support ticket system already has. --}}
            @if ($booking->supportQuery)
            <h3 class="font-semibold text-gray-700 pt-2">{{ __('db.Messages') }}</h3>
            @foreach ($booking->supportQuery->responses->where('is_internal', false) as $response)
            @php $isMe = $response->responder_id === auth()->id(); @endphp
            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-lg rounded-2xl p-4 text-sm {{ $isMe ? 'bg-teal-600 text-white' : 'bg-white text-gray-800 shadow-sm' }}">
                    <p class="font-semibold text-xs mb-1 {{ $isMe ? 'text-teal-100' : 'text-gray-400' }}">
                        {{ $isMe ? __('db.You') : ($booking->isAnonymous() ? __('db.Member') : $response->responder->name) }}
                    </p>
                    <p class="leading-relaxed">{{ $response->message }}</p>
                    <p class="text-xs mt-2 {{ $isMe ? 'text-teal-200' : 'text-gray-400' }}">{{ $response->created_at->format('d M, h:i A') }}</p>
                </div>
            </div>
            @endforeach

            <div class="bg-white rounded-2xl shadow-sm p-5">
                <form method="POST" action="{{ route('counselor.bookings.reply', $booking) }}">
                    @csrf
                    <textarea name="message" rows="3" required maxlength="2000"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-teal-500 resize-none mb-3"
                        placeholder="{{ __('db.Write a message...') }}"></textarea>
                    <button class="btn-base btn-teal px-6 py-2 text-sm font-semibold">{{ __('db.Send') }}</button>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
