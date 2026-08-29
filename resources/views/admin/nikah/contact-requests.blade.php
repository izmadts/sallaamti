<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Matchmaker Requests</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            <div class="flex gap-2 border-b border-gray-200">
                <a href="{{ route('admin.nikah.contact-requests') }}"
                   class="px-4 py-2 text-sm font-semibold border-b-2 -mb-px {{ $type === 'contact' ? 'border-teal-600 text-teal-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Contact Requests
                    @if ($pendingContactCount > 0)<span class="ml-1 text-xs px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800">{{ $pendingContactCount }}</span>@endif
                </a>
                <a href="{{ route('admin.nikah.contact-requests', ['type' => 'card']) }}"
                   class="px-4 py-2 text-sm font-semibold border-b-2 -mb-px {{ $type === 'card' ? 'border-teal-600 text-teal-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Card Dispatch Requests
                    @if ($pendingCardCount > 0)<span class="ml-1 text-xs px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800">{{ $pendingCardCount }}</span>@endif
                </a>
            </div>

            @if ($type === 'card')

            <p class="text-sm text-gray-500">Counselors who tapped "Request Card Dispatch" in the app, asking for their physical ID card to be printed and mailed.</p>

            @forelse ($cardRequests as $app)
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex justify-between items-start gap-4 flex-wrap">
                    <div>
                        <p class="font-semibold text-gray-800">
                            {{ $app->full_name }}
                            @if ($app->counselor_code)<span class="text-gray-400 font-normal">· {{ $app->counselor_code }}</span>@endif
                        </p>
                        @if ($app->address)
                        <p class="text-sm text-gray-500 mt-1">{{ collect([$app->address, $app->area, $app->country])->filter()->implode(', ') }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Requested {{ $app->card_requested_at->diffForHumans() }}</p>
                        @if ($app->card_dispatched_at)
                        <p class="text-xs text-green-700">Dispatched {{ $app->card_dispatched_at->diffForHumans() }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $app->card_dispatched_at ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $app->card_dispatched_at ? 'Dispatched' : 'Pending' }}
                        </span>
                        <a href="{{ route('admin.matchmaker-applications.show', $app) }}" class="text-xs text-teal-700 hover:underline">View Counselor →</a>
                    </div>
                </div>

                @unless ($app->card_dispatched_at)
                <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                    <form method="POST" action="{{ route('admin.matchmaker-applications.card-dispatched', $app) }}">
                        @csrf
                        <button class="bg-teal-600 text-white text-xs px-3 py-1.5 rounded hover:bg-teal-700">Mark Dispatched</button>
                    </form>
                </div>
                @endunless
            </div>
            @empty
            <div class="text-center py-16 bg-white rounded-2xl shadow-sm">
                <div class="text-4xl mb-2">📮</div>
                <p class="text-gray-500">No card dispatch requests yet.</p>
            </div>
            @endforelse

            {{ $cardRequests->links() }}

            @else

            <p class="text-sm text-gray-500">
                Only real admins can approve or deny these — a matchmaker's own nikah.manage-less account can never see this queue or act on it.
            </p>

            @forelse ($requests as $req)
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex justify-between items-start gap-4 flex-wrap">
                    <div>
                        <p class="font-semibold text-gray-800">
                            {{ $req->requester?->name ?? 'Deleted account' }}
                            <span class="text-gray-400 font-normal">requested contact for</span>
                            {{ $req->profile?->age }} yrs, {{ ucfirst($req->profile?->user?->gender ?? '—') }} — {{ $req->profile?->city }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Requested {{ $req->created_at->diffForHumans() }}</p>
                        @if ($req->decided_at)
                        <p class="text-xs text-gray-400">Decided {{ $req->decided_at->diffForHumans() }} by {{ $req->decider?->name ?? '—' }}</p>
                        @endif
                        @if ($req->admin_notes)
                        <p class="text-xs text-gray-500 mt-1 italic">"{{ $req->admin_notes }}"</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 rounded-full font-semibold
                            {{ $req->status === 'approved' ? 'bg-green-100 text-green-700' :
                               ($req->status === 'denied' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($req->status) }}
                        </span>
                        @if ($req->profile)
                        <a href="{{ route('admin.nikah.show', $req->profile) }}" class="text-xs text-teal-700 hover:underline">View Profile →</a>
                        @endif
                    </div>
                </div>

                @if ($req->status === 'pending')
                <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                    <form method="POST" action="{{ route('admin.nikah.contact-requests.approve', $req) }}">
                        @csrf
                        <button class="bg-green-600 text-white text-xs px-3 py-1.5 rounded hover:bg-green-700">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.nikah.contact-requests.deny', $req) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="admin_notes" placeholder="Reason (optional)" class="border-gray-300 rounded text-xs px-2">
                        <button class="bg-red-600 text-white text-xs px-3 py-1.5 rounded hover:bg-red-700">Deny</button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-16 bg-white rounded-2xl shadow-sm">
                <div class="text-4xl mb-2">📭</div>
                <p class="text-gray-500">No contact requests yet.</p>
            </div>
            @endforelse

            {{ $requests->links() }}

            @endif

        </div>
    </div>
</x-admin-layout>
