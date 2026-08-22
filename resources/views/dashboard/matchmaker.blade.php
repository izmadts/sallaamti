<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Matchmaker Panel — {{ $user->name }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-xl p-4 flex items-start gap-3" style="background: var(--teal-light); border: 1px solid #0d6b6b33">
                <span class="text-xl">🛡️</span>
                <p class="text-sm text-gray-700">
                    You can browse every profile, but contact details, CNIC, and photos stay hidden — request contact from admin when you find a good match.
                </p>
            </div>

            {{-- Quick actions --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.leads.create') }}" class="text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90" style="background: #be185d">+ Add Lead</a>
                <a href="{{ route('admin.leads.index') }}" class="text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90" style="background: #0d6b6b">📞 My Leads</a>
                <a href="{{ route('matchmaker.nikah.index') }}" class="text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50">🔍 Browse Nikah Profiles</a>
                <a href="{{ route('matchmaker.nikah.requests') }}" class="text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50">🤝 My Contact Requests</a>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-blue-400">
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['new_leads'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">New Leads</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-red-400">
                    <p class="text-2xl font-bold text-red-600">{{ $stats['follow_ups_due'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Follow-ups Due</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-green-400">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['registered_leads'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Registered Clients</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-teal-400">
                    <p class="text-2xl font-bold text-teal-700">{{ $stats['total_profiles'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Active Profiles</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-yellow-400">
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_requests'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Pending Requests</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-green-400">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['approved_requests'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Approved for You</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Follow-ups due --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-3">📅 Follow-ups Due</h3>
                    @forelse ($followUps as $lead)
                    <a href="{{ route('admin.leads.show', $lead) }}" class="flex justify-between items-center py-2.5 border-b last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded">
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
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-3">📞 Recent Leads</h3>
                    @forelse ($recentLeads as $lead)
                    <a href="{{ route('admin.leads.show', $lead) }}" class="flex justify-between items-center py-2.5 border-b last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded">
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
                    <p class="text-sm text-gray-400">No leads yet. <a href="{{ route('admin.leads.create') }}" class="text-teal-600 hover:underline">Add your first one</a>.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
