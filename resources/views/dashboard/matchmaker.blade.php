<x-matchmaker-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Match Maker Desk — {{ $user->name }}</h2>
        <p class="text-sm text-gray-500">Your clients, proposals, and responses in one place.</p>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">

        <div class="rounded-xl p-4 flex items-start gap-3" style="background: var(--teal-light); border: 1px solid #0d6b6b33">
            <span class="text-xl">🛡️</span>
            <p class="text-sm text-gray-700">
                Contact details, CNIC, and photos stay hidden everywhere in this workspace. Proposal links you send are logged (time, device, approximate location) for everyone's safety.
            </p>
        </div>

        <a href="{{ route('guide.index') }}" class="rounded-xl p-4 flex items-center gap-3 bg-white border border-gray-200 hover:border-teal-300 hover:shadow-sm transition group">
            <span class="text-2xl">📘</span>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800 group-hover:text-teal-700">New here, or need a refresher? Read the full Match Maker Guide</p>
                <p class="text-xs text-gray-500">Your role, every step from adding a client to sending proposals, and the security rules to follow — in English and اردو.</p>
            </div>
            <span class="text-teal-600 text-sm font-semibold">Open →</span>
        </a>

        {{-- Quick actions --}}
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('matchmaker.clients.create') }}" class="text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm" style="background: var(--mm-plum);">➕ Add Client</a>
            <a href="{{ route('matchmaker.clients.index') }}" class="text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm" style="background: var(--mm-plum-dark);">🗂️ My Clients</a>
            <a href="{{ route('matchmaker.nikah.index') }}" class="text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm" style="background: var(--mm-gold);">🔎 Browse Nikah Profiles</a>
            <a href="{{ route('matchmaker.nikah.requests') }}" class="text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition">📨 My Contact Requests</a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-blue-400">
                <p class="text-2xl font-bold text-blue-600">{{ $stats['new_leads'] }}</p>
                <p class="text-xs text-gray-500 mt-1">New Leads</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-red-400">
                <p class="text-2xl font-bold text-red-600">{{ $stats['follow_ups_due'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Follow-ups Due</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-green-400">
                <p class="text-2xl font-bold text-green-600">{{ $stats['registered_leads'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Registered Clients</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4" style="border-color: var(--mm-plum);">
                <p class="text-2xl font-bold" style="color: var(--mm-plum);">{{ $stats['active_batches'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Active Proposal Batches</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4" style="border-color: var(--mm-gold);">
                <p class="text-2xl font-bold" style="color: #a5810f;">{{ $stats['awaiting_response'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Awaiting Client Response</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-emerald-400">
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['interested_this_week'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Interested (7 days)</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-teal-400">
                <p class="text-2xl font-bold text-teal-700">{{ $stats['total_profiles'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Active Profiles</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-yellow-400">
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_requests'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Pending Contact Requests</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-green-400">
                <p class="text-2xl font-bold text-green-600">{{ $stats['approved_requests'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Approved for You</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Follow-ups due --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">📅 Follow-ups Due</h3>
                @forelse ($followUps as $lead)
                <a href="{{ route('matchmaker.clients.show', $lead) }}" class="flex justify-between items-center py-2.5 border-b last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded">
                    <div>
                        <p class="font-medium text-gray-800 text-sm">{{ $lead->name }}</p>
                        <p class="text-xs text-gray-400">{{ $lead->phone ?: $lead->email }}</p>
                    </div>
                    <span class="text-xs {{ $lead->next_follow_up_at->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        {{ $lead->next_follow_up_at->format('d M') }}
                    </span>
                </a>
                @empty
                <p class="text-sm text-gray-400">Nothing due — you're caught up.</p>
                @endforelse
            </div>

            {{-- Recent leads --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">📞 Recent Clients</h3>
                @forelse ($recentLeads as $lead)
                <a href="{{ route('matchmaker.clients.show', $lead) }}" class="flex justify-between items-center py-2.5 border-b last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded">
                    <div>
                        <p class="font-medium text-gray-800 text-sm">{{ $lead->name }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ $lead->source }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ match($lead->status) {
                            'new' => 'bg-blue-100 text-blue-800',
                            'contacted' => 'bg-amber-100 text-amber-800',
                            'interested' => 'bg-purple-100 text-purple-800',
                            'registered' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-600',
                        } }}">
                        {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                    </span>
                </a>
                @empty
                <p class="text-sm text-gray-400">No clients yet. <a href="{{ route('matchmaker.clients.create') }}" class="hover:underline" style="color: var(--mm-plum);">Add your first one</a>.</p>
                @endforelse
            </div>

            {{-- Recent activity --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">🕓 Recent Activity</h3>
                @forelse ($recentActivity as $event)
                <div class="py-2.5 border-b last:border-0">
                    <p class="text-sm text-gray-800">{{ $event->description }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $event->created_at->diffForHumans() }}</p>
                </div>
                @empty
                <p class="text-sm text-gray-400">No activity logged yet.</p>
                @endforelse
            </div>
        </div>

    </div>
</x-matchmaker-layout>
