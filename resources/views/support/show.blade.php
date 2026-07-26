<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ $query->subject }}</h2>
            <a href="{{ route('support.index') }}" class="text-sm text-gray-500 hover:underline">← My Queries</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-xl">✅ {{ session('status') }}</div>
            @endif

            {{-- Query Details --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-full mr-2" style="background: var(--teal-light); color: var(--teal)">
                            {{ ucfirst($query->category) }}
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-full
                            {{ $query->priority === 'high' ? 'bg-red-100 text-red-700' :
                               ($query->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                            {{ ucfirst($query->priority) }} Priority
                        </span>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full
                        {{ $query->status === 'resolved' ? 'bg-green-100 text-green-700' :
                           ($query->status === 'in_progress' ? 'bg-purple-100 text-purple-700' :
                           ($query->status === 'assigned' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                        {{ ucfirst(str_replace('_', ' ', $query->status)) }}
                    </span>
                </div>

                <p class="text-gray-600 leading-relaxed">{{ $query->description }}</p>
                <p class="text-xs text-gray-400 mt-3">
                    Submitted {{ $query->created_at->format('d M Y, h:i A') }}
                    @if ($query->assignedTo)
                    · Assigned to {{ $query->assignedTo->name }}
                    @endif
                </p>
            </div>

            {{-- Conversation Thread --}}
            @foreach ($query->responses->where('is_internal', false) as $response)
            @php $isMe = $response->responder_id === auth()->id(); @endphp
            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-lg rounded-2xl p-4 text-sm {{ $isMe ? 'bg-teal-600 text-white' : 'bg-white text-gray-800 shadow-sm' }}">
                    <p class="font-semibold text-xs mb-1 {{ $isMe ? 'text-teal-100' : 'text-gray-400' }}">
                        {{ $isMe ? 'You' : $response->responder->name }}
                    </p>
                    <p class="leading-relaxed">{{ $response->message }}</p>
                    <p class="text-xs mt-2 {{ $isMe ? 'text-teal-200' : 'text-gray-400' }}">
                        {{ $response->created_at->format('d M, h:i A') }}
                    </p>
                </div>
            </div>
            @endforeach

            {{-- Reply form --}}
            @if (in_array($query->status, ['assigned', 'in_progress']))
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <h5 class="font-semibold text-gray-700 mb-3 text-sm">Your Reply</h5>
                <form method="POST" action="{{ route('support.reply', $query) }}">
                    @csrf
                    <textarea name="message" rows="3" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-teal-500 resize-none mb-3"
                        placeholder="Write your reply..."></textarea>
                    <button class="btn-base btn-teal px-6 py-2 text-sm font-semibold">
                        Send Reply <i class="fa fa-paper-plane ml-1"></i>
                    </button>
                </form>
            </div>
            @elseif ($query->status === 'new')
            <div class="rounded-xl p-4 text-sm text-center" style="background: var(--cream)">
                ⏳ Your query is pending assignment. Our team will respond soon.
            </div>
            @elseif ($query->status === 'resolved')
            <div class="rounded-xl p-4 text-center" style="background: #f0fdf4; border: 1px solid #bbf7d0">
                <p class="text-green-700 font-semibold">✅ This query has been resolved.</p>
                <a href="{{ route('support.create') }}" class="text-sm text-green-600 hover:underline mt-1 inline-block">Submit a new query</a>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>