<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leads</h2>
            <a href="{{ route('admin.leads.create') }}" class="text-sm font-semibold px-4 py-2 rounded-lg text-white hover:opacity-90" style="background: #0d6b6b">+ Add Lead</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-blue-600">{{ $stats['new'] }}</p>
                    <p class="text-xs text-gray-500">New</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-amber-600">{{ $stats['contacted'] }}</p>
                    <p class="text-xs text-gray-500">Contacted</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-green-600">{{ $stats['registered'] }}</p>
                    <p class="text-xs text-gray-500">Profile Pending</p>
                </div>
                <a href="{{ route('admin.leads.index', ['follow_ups_due' => 1]) }}" class="bg-white rounded-lg shadow-sm p-3 text-center hover:bg-gray-50 {{ request()->boolean('follow_ups_due') ? 'ring-2 ring-red-300' : '' }}">
                    <p class="text-xl font-bold text-red-600">{{ $stats['follow_ups_due'] }}</p>
                    <p class="text-xs text-gray-500">Follow-ups Due</p>
                </a>
                <a href="{{ route('admin.leads.index', ['payment_status' => 'submitted']) }}" class="bg-white rounded-lg shadow-sm p-3 text-center hover:bg-gray-50">
                    <p class="text-xl font-bold text-purple-600">{{ $stats['payment_due'] }}</p>
                    <p class="text-xs text-gray-500">Payment Due</p>
                </a>
            </div>

            {{-- Status chips (owner, 12 Nov 2026): colour per status, count, one tap = only that status.
                 Ticking several in the form below filters by all of them together. --}}
            <div class="bg-white p-3 rounded-lg shadow-sm">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.leads.index', request()->except(['status', 'page'])) }}"
                       class="text-xs font-semibold px-3 py-1.5 rounded-full border {{ empty($statuses) ? 'border-gray-700' : 'border-gray-200' }}"
                       style="background:#ffffff;color:#1f2937">All · {{ array_sum($statusCounts) }}</a>
                    @foreach (\App\Models\Lead::STATUSES as $value => $st)
                    <a href="{{ route('admin.leads.index', array_merge(request()->except(['status', 'page']), ['status' => [$value]])) }}"
                       class="text-xs font-semibold px-3 py-1.5 rounded-full border-2 {{ in_array($value, $statuses, true) ? '' : 'border-transparent' }}"
                       style="{{ \App\Models\Lead::statusStyle($value) }};{{ in_array($value, $statuses, true) ? 'border-color:#111827' : '' }}"
                       title="{{ $st['auto'] ? 'System sets this automatically from the account/profile' : 'Set by staff' }}">
                        {{ $st['label'] }} · {{ $statusCounts[$value] ?? 0 }}
                    </a>
                    @endforeach
                </div>
            </div>

            <form method="GET" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs text-gray-500">Search (name, phone, email)</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="border-gray-300 rounded text-sm block w-56">
                </div>
                <details class="relative" {{ count($statuses) > 1 ? 'open' : '' }}>
                    <summary class="text-xs text-gray-500 cursor-pointer select-none">Status ({{ $statuses ? count($statuses) . ' selected' : 'All' }}) ▾</summary>
                    <div class="mt-1 grid grid-cols-2 gap-x-4 gap-y-1 bg-white border border-gray-200 rounded p-2">
                        @foreach (\App\Models\Lead::statusOptions() as $value => $label)
                        <label class="flex items-center gap-1.5 text-xs">
                            <input type="checkbox" name="status[]" value="{{ $value }}" {{ in_array($value, $statuses, true) ? 'checked' : '' }} class="rounded">
                            <span class="px-1.5 py-0.5 rounded-full" style="{{ \App\Models\Lead::statusStyle($value) }}">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </details>
                <div>
                    <label class="text-xs text-gray-500">Sort</label>
                    <select name="sort" class="border-gray-300 rounded text-sm block">
                        <option value="priority" {{ $sort === 'priority' ? 'selected' : '' }}>Priority (follow-ups due, then status)</option>
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest first</option>
                        <option value="follow_up" {{ $sort === 'follow_up' ? 'selected' : '' }}>Follow-up date</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Assigned To</label>
                    <select name="assigned_to" class="border-gray-300 rounded text-sm block">
                        <option value="">Anyone</option>
                        @foreach ($matchmakers as $mm)
                        <option value="{{ $mm->id }}" {{ (string) request('assigned_to') === (string) $mm->id ? 'selected' : '' }}>{{ $mm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Package Payment</label>
                    <select name="payment_status" class="border-gray-300 rounded text-sm block">
                        <option value="">Any</option>
                        <option value="submitted" {{ request('payment_status') === 'submitted' ? 'selected' : '' }}>Awaiting Review</option>
                        <option value="confirmed" {{ request('payment_status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="rejected" {{ request('payment_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <label class="flex items-center gap-1.5 text-sm text-gray-600 pb-2">
                    <input type="checkbox" name="my_leads" value="1" {{ request('my_leads') ? 'checked' : '' }} class="rounded"> My leads only
                </label>
                <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded">Filter</button>
                <a href="{{ route('admin.leads.index') }}" class="text-sm text-gray-500 px-2">Reset</a>
            </form>

            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Contact</th>
                            <th class="px-4 py-3 text-left">Source</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Assigned</th>
                            <th class="px-4 py-3 text-left">Follow-up</th>
                            <th class="px-4 py-3 text-left">Package</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($leads as $lead)
                        @php($followUpDue = $lead->next_follow_up_at && $lead->next_follow_up_at->lte(today()) && ! in_array($lead->status, \App\Models\Lead::NO_FOLLOW_UP_STATUSES))
                        <tr class="{{ $followUpDue ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }} cursor-pointer" onclick="window.location='{{ route('admin.leads.show', $lead) }}'">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $lead->name }}</p>
                                @if ($lead->isConverted())
                                <span class="text-xs text-green-600">✓ Has profile</span>
                                @endif
                            </td>
                            {{-- Same 💬 WhatsApp link + copy icon as the Nikah profile pages; stopPropagation keeps a tap on
                                 them from also opening the lead (the whole row is a link). --}}
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap" onclick="event.stopPropagation()">
                                @if ($lead->phone)
                                <x-phone-actions :phone="$lead->phone" />
                                @else
                                {{ $lead->email ?: '—' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 capitalize">{{ $lead->source }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full font-semibold whitespace-nowrap" style="{{ \App\Models\Lead::statusStyle($lead->status) }}">
                                    {{ \App\Models\Lead::statusLabel($lead->status) }}
                                </span>
                                {{-- Matchmaking-process journey, independent of the lifecycle
                                     status above — a quick-glance read of how far this lead
                                     has actually moved through requirements → shortlist →
                                     consent → proposal → match, computed as EXISTS/COUNT
                                     columns on the query above (no per-row queries here). --}}
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @if ($lead->has_matched_proposal)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-pink-100 text-pink-700 font-semibold">💍 Matched</span>
                                    @elseif ($lead->has_sent_batch)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-semibold">📤 Proposal Sent</span>
                                    @elseif ($lead->has_participation_consent)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-teal-100 text-teal-700 font-semibold">✅ Consent Given</span>
                                    @elseif ($lead->shortlist_items_count > 0)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">📝 Shortlisted ({{ $lead->shortlist_items_count }})</span>
                                    @elseif ($lead->requirement_exists)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-600 font-semibold">📋 Requirements Set</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $lead->assignedTo?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs {{ $lead->next_follow_up_at && $lead->next_follow_up_at->isPast() ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                {{ $lead->next_follow_up_at?->format('d M Y') ?? '—' }}
                                @if ($followUpDue)
                                <span class="block text-[10px] uppercase tracking-wide">due</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $lead->nikahPackage?->name ?? '—' }}
                                @if ($lead->package_payment_status === 'submitted')
                                <span class="block mt-1 text-purple-700 bg-purple-100 px-1.5 py-0.5 rounded-full text-[11px] font-semibold w-fit">💵 Payment Due</span>
                                @elseif ($lead->package_payment_status === 'rejected')
                                <span class="block mt-1 text-red-700 bg-red-100 px-1.5 py-0.5 rounded-full text-[11px] font-semibold w-fit">Payment Rejected</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No leads match your filters.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $leads->links() }}
        </div>
    </div>
</x-admin-layout>
