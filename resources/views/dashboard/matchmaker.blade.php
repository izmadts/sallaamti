<x-matchmaker-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nikah Counselor Desk — {{ $user->name }}</h2>
        <p class="text-sm text-gray-500">Your clients, proposals, and responses in one place.</p>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">

        <a href="{{ route('guide.index') }}" class="rounded-xl p-4 flex items-center gap-3 bg-white border border-gray-200 hover:border-teal-300 hover:shadow-sm transition group">
            <span class="text-2xl">📘</span>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800 group-hover:text-teal-700">New here, or need a refresher? Read the full Nikah Counselor Guide</p>
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

        {{-- Headline numbers — trimmed from the previous 8-tile grid to the
             ones a counselor actually acts on day to day; everything else
             (profiles pool, contact requests, weekly interest) still lives
             on Browse/Contact Requests/Performance where it's actionable. --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-blue-400">
                <p class="text-2xl font-bold text-blue-600">{{ $stats['new_leads'] }}</p>
                <p class="text-xs text-gray-500 mt-1">New Leads</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-red-400">
                <p class="text-2xl font-bold text-red-600">{{ $stats['follow_ups_due'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Follow-ups Due</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4" style="border-color: var(--mm-plum);">
                <p class="text-2xl font-bold" style="color: var(--mm-plum);">{{ $stats['active_batches'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Active Proposal Batches</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-green-400">
                <p class="text-2xl font-bold text-green-600">{{ $stats['registered_leads'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Registered Clients</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Needs Your Attention — the reminder system: every open client,
                 grouped by whatever stage is blocking them next, computed
                 live from LeadJourney so it's never stale. --}}
            <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
                <h3 class="font-semibold text-gray-700 mb-1">🔔 Needs Your Attention</h3>
                <p class="text-xs text-gray-400 mb-3">Every open client, grouped by what's next for them.</p>

                @if ($attentionGroups->isEmpty())
                <p class="text-sm text-gray-400">Nothing needs attention right now — you're caught up.</p>
                @else
                <div class="space-y-4">
                    @foreach ($attentionGroups as $stageKey => $leads)
                    @php $stageMeta = \App\Support\LeadJourney::STAGES[$stageKey]; @endphp
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            {{ $stageMeta['icon'] }} {{ $stageMeta['label'] }} <span class="text-gray-300">({{ $leads->count() }})</span>
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($leads->take(6) as $lead)
                            <a href="{{ route('matchmaker.clients.show', $lead) }}" class="text-xs bg-gray-50 hover:bg-gray-100 border border-gray-100 rounded-full px-3 py-1.5 text-gray-700 transition">
                                {{ $lead->name }}
                            </a>
                            @endforeach
                            @if ($leads->count() > 6)
                            <a href="{{ route('matchmaker.clients.index') }}" class="text-xs px-3 py-1.5 text-gray-400 hover:underline">+{{ $leads->count() - 6 }} more</a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Follow-ups due --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">📅 Follow-ups Due</h3>
                @forelse ($followUps as $lead)
                <a href="{{ route('matchmaker.clients.show', $lead) }}" class="flex justify-between items-center py-2.5 border-b last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded">
                    <div>
                        <p class="font-medium text-gray-800 text-sm">{{ $lead->name }}</p>
                        <p class="text-xs text-gray-400">{{ $lead->visiblePhoneFor(auth()->user()) ?: $lead->email }}</p>
                    </div>
                    <span class="text-xs {{ $lead->next_follow_up_at->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        {{ $lead->next_follow_up_at->format('d M') }}
                    </span>
                </a>
                @empty
                <p class="text-sm text-gray-400">Nothing due — you're caught up.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent activity --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-3">🕓 Recent Activity</h3>
            @forelse ($recentActivity as $event)
            <div class="py-2.5 border-b last:border-0 flex justify-between items-center gap-3">
                <p class="text-sm text-gray-800">{{ $event->description }}</p>
                <p class="text-xs text-gray-400 whitespace-nowrap">{{ $event->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p class="text-sm text-gray-400">No activity logged yet.</p>
            @endforelse
        </div>

    </div>
</x-matchmaker-layout>
