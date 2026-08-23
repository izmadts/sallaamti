<x-matchmaker-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">My Clients</h2>
        <p class="text-sm">Everyone currently assigned to you.</p>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 flex-1">
                <div class="bg-white rounded-xl shadow-sm p-3 text-center border-t-4 border-blue-400">
                    <p class="text-xl font-bold text-blue-600">{{ $stats['new'] }}</p>
                    <p class="text-xs text-gray-500">New</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-3 text-center border-t-4 border-amber-400">
                    <p class="text-xl font-bold text-amber-600">{{ $stats['contacted'] }}</p>
                    <p class="text-xs text-gray-500">Contacted</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-3 text-center border-t-4 border-green-400">
                    <p class="text-xl font-bold text-green-600">{{ $stats['registered'] }}</p>
                    <p class="text-xs text-gray-500">Registered</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-3 text-center border-t-4 border-red-400">
                    <p class="text-xl font-bold text-red-600">{{ $stats['follow_ups_due'] }}</p>
                    <p class="text-xs text-gray-500">Follow-ups Due</p>
                </div>
            </div>
            <a href="{{ route('matchmaker.clients.create') }}" class="text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm whitespace-nowrap" style="background: var(--mm-plum);">➕ Add Client</a>
        </div>

        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Status</label>
                    <select name="status" class="border-gray-300 rounded-lg text-sm">
                        <option value="">Any</option>
                        @foreach (['new' => 'New', 'contacted' => 'Contacted', 'interested' => 'Interested', 'registered' => 'Registered', 'not_interested' => 'Not Interested', 'closed' => 'Closed'] as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, phone, email…" class="border-gray-300 rounded-lg text-sm w-56">
                </div>
                <button class="text-sm font-semibold px-4 py-2 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum);">Filter</button>
                @if (request()->hasAny(['status', 'search']))
                <a href="{{ route('matchmaker.clients.index') }}" class="text-sm text-gray-500 px-2 py-2 hover:underline">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-gray-500 border-b" style="background: #FBF6F9;">
                            <th class="px-4 py-3">Client</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Follow-up</th>
                            <th class="px-4 py-3">Linked Profile</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($clients as $lead)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $lead->name }}</p>
                                <p class="text-xs text-gray-400">{{ $lead->phone ?: $lead->email ?: '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
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
                            </td>
                            <td class="px-4 py-3 text-xs {{ $lead->next_follow_up_at && $lead->next_follow_up_at->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                {{ $lead->next_follow_up_at?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $lead->nikahProfile?->user?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('matchmaker.clients.show', $lead) }}" class="text-sm font-semibold hover:underline" style="color: var(--mm-plum);">Open →</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No clients yet. <a href="{{ route('matchmaker.clients.create') }}" class="hover:underline" style="color: var(--mm-plum);">Add your first one</a>.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($clients->hasPages())
            <div class="px-4 py-3 border-t">{{ $clients->links() }}</div>
            @endif
        </div>

    </div>
</x-matchmaker-layout>
