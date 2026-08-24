<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.commissions.rules') }}" class="text-gray-400 hover:text-gray-600">← Commission Rules</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Ledger</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            {{-- Grant a bonus --}}
            <div class="bg-white rounded-xl shadow-sm p-5" style="border-top: 3px solid #b8962e">
                <h4 class="font-semibold text-gray-700 mb-3">🏆 Grant Recognition Bonus</h4>
                <p class="text-xs text-gray-500 mb-3">A discretionary award for a documented successful outcome — never automatic, always with a note.</p>
                <form method="POST" action="{{ route('admin.commissions.bonus') }}" class="flex flex-wrap gap-2 items-end">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Counselor</label>
                        <select name="matchmaker_id" required class="border-gray-300 rounded-md text-sm">
                            <option value="">Select</option>
                            @foreach ($matchmakers as $mm)
                            <option value="{{ $mm->id }}">{{ $mm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Amount (Rs.)</label>
                        <input type="number" name="amount" min="1" required class="border-gray-300 rounded-md text-sm w-28">
                    </div>
                    <div class="flex-1 min-w-[14rem]">
                        <label class="text-xs text-gray-500 block mb-1">Note (required)</label>
                        <input type="text" name="notes" required class="border-gray-300 rounded-md text-sm w-full" placeholder="e.g. Sallaamti Nikah Success Award — [client name] got married">
                    </div>
                    <button class="text-sm font-semibold px-4 py-2 rounded-lg text-white hover:opacity-90" style="background: #b8962e">Grant Bonus</button>
                </form>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total Pending (all counselors)</p>
                    <p class="text-2xl font-bold text-amber-600">Rs. {{ number_format($totals['pending'], 2) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total Approved, Not Yet Paid</p>
                    <p class="text-2xl font-bold" style="color: #0d6b6b">Rs. {{ number_format($totals['approved'], 2) }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-lg shadow-sm p-4">
                <form method="GET" class="flex flex-wrap gap-2 items-end">
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Status</label>
                        <select name="status" class="border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Counselor</label>
                        <select name="matchmaker_id" class="border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach ($matchmakers as $mm)
                            <option value="{{ $mm->id }}" {{ (string) request('matchmaker_id') === (string) $mm->id ? 'selected' : '' }}>{{ $mm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- Entries --}}
            <div class="bg-white rounded-xl shadow-sm divide-y">
                @forelse ($entries as $entry)
                <div class="p-5 flex flex-wrap justify-between items-start gap-3 {{ $entry->isFlagged() ? 'bg-red-50/50' : '' }}">
                    <div>
                        <p class="font-medium text-gray-800">{{ $entry->matchmaker->name }}
                            <span class="text-xs font-normal text-gray-400">· {{ match($entry->rule_type) {
                                'verified_profile' => 'Verified Profile',
                                'recognition_bonus' => '🏆 Recognition Bonus',
                                default => ($entry->nikahPackage?->name ?? 'Package') . ' (' . ($entry->is_renewal ? 'Renewal' : 'First Purchase') . ')',
                            } }}</span>
                        </p>
                        <p class="text-sm text-gray-500">Rs. {{ number_format($entry->commission_amount, 2) }}
                            <span class="text-gray-400">· {{ \App\Models\MatchmakerApplication::LEVELS[$entry->tier_at_time] ?? $entry->tier_at_time }}</span>
                        </p>
                        @if ($entry->notes)
                        <p class="text-xs text-gray-500 mt-1 italic">"{{ $entry->notes }}"</p>
                        @endif
                        @if ($entry->isFlagged())
                        <p class="text-xs text-red-600 mt-1">🚩 Flagged: {{ $entry->flag_reason }} (by {{ $entry->flaggedBy?->name }})</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Created {{ $entry->created_at->format('d M Y') }}
                            @if ($entry->status === 'pending' && $entry->eligible_at)
                            · Eligible for approval from {{ $entry->eligible_at->format('d M Y') }}
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs px-2 py-1 rounded-full font-semibold
                            {{ match($entry->status) {
                                'paid' => 'bg-green-100 text-green-800',
                                'approved' => 'bg-blue-100 text-blue-800',
                                default => 'bg-amber-100 text-amber-800',
                            } }}">
                            {{ ucfirst($entry->status) }}
                        </span>

                        <div class="flex gap-1.5 flex-wrap justify-end">
                            @if ($entry->status === 'pending' && !$entry->isFlagged())
                            <form method="POST" action="{{ route('admin.commissions.approve', $entry) }}">
                                @csrf
                                <button {{ $entry->isEligibleForApproval() ? '' : 'disabled' }} class="text-xs font-semibold px-2.5 py-1 rounded-lg text-white disabled:opacity-40 disabled:cursor-not-allowed" style="background: #0d6b6b" title="{{ $entry->isEligibleForApproval() ? '' : 'Not eligible until the hold date passes' }}">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.commissions.flag', $entry) }}" onsubmit="return promptFlag(this)">
                                @csrf
                                <input type="hidden" name="flag_reason" value="">
                                <button class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-red-300 text-red-600 hover:bg-red-50">Flag</button>
                            </form>
                            @endif
                            @if ($entry->isFlagged())
                            <form method="POST" action="{{ route('admin.commissions.unflag', $entry) }}">
                                @csrf
                                <button class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">Clear Flag</button>
                            </form>
                            @endif
                            @if ($entry->status === 'approved')
                            <form method="POST" action="{{ route('admin.commissions.pay', $entry) }}" onsubmit="return confirm('Confirm you have already sent Rs. {{ number_format($entry->commission_amount, 2) }} to {{ $entry->matchmaker->name }}?')">
                                @csrf
                                <button class="text-xs font-semibold px-2.5 py-1 rounded-lg text-white hover:opacity-90" style="background: #b8962e">Mark Paid</button>
                            </form>
                            @endif
                            @if ($entry->status === 'pending' && $entry->rule_type === 'package')
                            <form method="POST" action="{{ route('admin.commissions.reclassify', $entry) }}">
                                @csrf
                                <input type="hidden" name="is_renewal" value="{{ $entry->is_renewal ? '0' : '1' }}">
                                <button class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">Mark as {{ $entry->is_renewal ? 'First Purchase' : 'Renewal' }}</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-500">No commission entries yet.</p>
                @endforelse
            </div>

            {{ $entries->links() }}
        </div>
    </div>

    <script>
        function promptFlag(form) {
            const reason = prompt('Why are you flagging this entry?');
            if (!reason) return false;
            form.querySelector('input[name="flag_reason"]').value = reason;
            return true;
        }
    </script>
</x-admin-layout>
