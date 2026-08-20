<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Matchmaker Contact Requests</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

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

        </div>
    </div>
</x-admin-layout>
