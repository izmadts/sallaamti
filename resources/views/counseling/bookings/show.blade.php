<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.Counseling Session') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            @if ($booking->status === 'requested')
            <div class="rounded-xl p-4 flex items-start gap-3" style="background: var(--teal-light); border: 1px solid #0d6b6b33">
                <span class="text-xl">🕊️</span>
                <p class="text-sm text-gray-700 leading-relaxed">
                    @if ($booking->counselor)
                        {{ __('db.Waiting for :name to confirm your session. You\'ll be notified as soon as it\'s confirmed, in sha Allah.', ['name' => $booking->counselor->name]) }}
                    @else
                        {{ __("db.Your request has been received. We'll assign a counselor and confirm your time shortly, in sha Allah.") }}
                    @endif
                </p>
            </div>
            @elseif ($booking->status === 'confirmed')
            <div class="rounded-xl p-4 flex items-start gap-3 bg-blue-50 border border-blue-200">
                <span class="text-xl">✅</span>
                <p class="text-sm text-gray-700 leading-relaxed">{{ __('db.Your session is confirmed. We look forward to speaking with you, in sha Allah.') }}</p>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-lg font-semibold text-gray-800">{{ $booking->scheduled_at->format('l, d M Y — h:i A') }}</p>
                        <p class="text-xs text-gray-400">{{ $booking->status === 'requested' ? __('db.Preferred time — pending confirmation') : __('db.Scheduled time') }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $booking->counselor?->name ?? __('db.Counselor to be assigned') }}</p>
                        @if ($booking->isUrgent())
                        <span class="text-xs font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full inline-block mt-1">🚨 {{ __('db.Flagged urgent') }}</span>
                        @endif
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

                @if ($booking->meeting_link)
                <div class="border-t pt-4 mt-4">
                    <p class="text-xs text-gray-400 mb-1">{{ __('db.Meeting Link') }}</p>
                    <a href="{{ $booking->meeting_link }}" target="_blank" class="text-sm text-teal-600 hover:underline break-all">{{ $booking->meeting_link }}</a>
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

            @if ($booking->status === 'completed')
            <div class="bg-white rounded-lg shadow-sm p-6">
                @if ($booking->member_rating)
                <p class="text-sm text-gray-700">{{ __('db.Your rating') }}:
                    <span class="text-amber-500">{{ str_repeat('★', $booking->member_rating) }}{{ str_repeat('☆', 5 - $booking->member_rating) }}</span>
                </p>
                @if ($booking->member_feedback)
                <p class="text-sm text-gray-500 mt-1">"{{ $booking->member_feedback }}"</p>
                @endif
                @else
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.How was your session?') }}</h3>
                <form method="POST" action="{{ route('counseling.bookings.rate', $booking) }}" x-data="{ rating: 0 }">
                    @csrf
                    <div class="flex gap-1 mb-3 text-2xl">
                        <template x-for="star in [1,2,3,4,5]" :key="star">
                            <button type="button" @click="rating = star" :class="star <= rating ? 'text-amber-500' : 'text-gray-300'" x-text="'★'"></button>
                        </template>
                    </div>
                    <input type="hidden" name="member_rating" x-model="rating">
                    <textarea name="member_feedback" rows="2" maxlength="2000" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="{{ __('db.Optional feedback...') }}"></textarea>
                    <x-primary-button class="mt-3">{{ __('db.Submit Rating') }}</x-primary-button>
                </form>
                @endif
            </div>
            @endif

            {{-- Message thread --}}
            @if ($booking->supportQuery)
            @php $thread = $booking->supportQuery->responses->where('is_internal', false); @endphp
            @if ($thread->isNotEmpty() || in_array($booking->status, ['requested', 'confirmed']))
            <h3 class="font-semibold text-gray-700 pt-2">{{ __('db.Messages') }}</h3>
            @foreach ($thread as $response)
            @php $isMe = $response->responder_id === auth()->id(); @endphp
            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-lg rounded-2xl p-4 text-sm {{ $isMe ? 'bg-teal-600 text-white' : 'bg-white text-gray-800 shadow-sm' }}">
                    <p class="font-semibold text-xs mb-1 {{ $isMe ? 'text-teal-100' : 'text-gray-400' }}">
                        {{ $isMe ? __('db.You') : ($booking->counselor?->name ?? __('db.Counselor')) }}
                    </p>
                    <p class="leading-relaxed">{{ $response->message }}</p>
                    <p class="text-xs mt-2 {{ $isMe ? 'text-teal-200' : 'text-gray-400' }}">{{ $response->created_at->format('d M, h:i A') }}</p>
                </div>
            </div>
            @endforeach

            @if (in_array($booking->status, ['requested', 'confirmed']))
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <form method="POST" action="{{ route('counseling.bookings.reply', $booking) }}">
                    @csrf
                    <textarea name="message" rows="3" required maxlength="2000"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-teal-500 resize-none mb-3"
                        placeholder="{{ __('db.Write a message to your counselor...') }}"></textarea>
                    <button class="btn-base btn-teal px-6 py-2 text-sm font-semibold">{{ __('db.Send') }}</button>
                </form>
            </div>
            @endif
            @endif
            @endif

        </div>
    </div>
</x-app-layout>
