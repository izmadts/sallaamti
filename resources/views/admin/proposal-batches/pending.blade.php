<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proposal Batches Awaiting Approval</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <p class="text-sm text-gray-500">A Probation-stage Nikah Counselor's "Send Batch" lands here first — approve to send it on to the client, or reject with a note to send it back to the counselor.</p>

            @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg p-3">{{ session('status') }}</div>
            @endif
            @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg p-3">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($batches as $batch)
                <div class="p-5 flex justify-between items-start flex-wrap gap-4">
                    <div>
                        <p class="font-medium text-gray-800">Batch #{{ $batch->batch_number }} — {{ $batch->lead?->name ?? 'Unknown client' }}</p>
                        <p class="text-sm text-gray-500">From {{ $batch->matchmaker?->name ?? 'Unknown counselor' }} · {{ $batch->proposals->count() }} candidate(s) · {{ $batch->updated_at->diffForHumans() }}</p>
                        <ul class="text-xs text-gray-500 mt-2 list-disc list-inside">
                            @foreach ($batch->proposals as $proposal)
                            <li>{{ $proposal->candidate?->user?->name ?? 'Candidate #' . $proposal->candidate_profile_id }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <form method="POST" action="{{ route('admin.proposal-batches.approve', $batch) }}">
                            @csrf
                            <button class="text-sm font-semibold px-4 py-2 rounded-lg text-white hover:opacity-90" style="background: #0d6b6b">Approve &amp; Send</button>
                        </form>
                        <button type="button" onclick="document.getElementById('reject-{{ $batch->id }}').classList.toggle('hidden')" class="text-sm font-semibold px-4 py-2 rounded-lg border border-red-300 text-red-700 hover:bg-red-50">Reject</button>
                    </div>
                    <form id="reject-{{ $batch->id }}" method="POST" action="{{ route('admin.proposal-batches.reject', $batch) }}" class="hidden w-full flex gap-2">
                        @csrf
                        <input type="text" name="reason" required maxlength="1000" placeholder="Why is this going back to the counselor?" class="flex-1 border-gray-300 rounded-md text-sm">
                        <button class="text-sm font-semibold px-4 py-2 rounded-lg bg-red-600 text-white hover:opacity-90">Send Back</button>
                    </form>
                </div>
                @empty
                <p class="p-5 text-sm text-gray-500">Nothing waiting for approval right now.</p>
                @endforelse
            </div>

            {{ $batches->links() }}
        </div>
    </div>
</x-admin-layout>
