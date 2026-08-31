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
                    <p class="text-xs text-gray-500">Registered</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-red-600">{{ $stats['follow_ups_due'] }}</p>
                    <p class="text-xs text-gray-500">Follow-ups Due</p>
                </div>
                <a href="{{ route('admin.leads.index', ['payment_status' => 'submitted']) }}" class="bg-white rounded-lg shadow-sm p-3 text-center hover:bg-gray-50">
                    <p class="text-xl font-bold text-purple-600">{{ $stats['payment_due'] }}</p>
                    <p class="text-xs text-gray-500">Payment Due</p>
                </a>
            </div>

            <form method="GET" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs text-gray-500">Search (name, phone, email)</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="border-gray-300 rounded text-sm block w-56">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Status</label>
                    <select name="status" class="border-gray-300 rounded text-sm block">
                        <option value="">All</option>
                        @foreach (['new' => 'New', 'contacted' => 'Contacted', 'interested' => 'Interested', 'registered' => 'Registered', 'not_interested' => 'Not Interested', 'closed' => 'Closed'] as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
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
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.leads.show', $lead) }}'">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $lead->name }}</p>
                                @if ($lead->isConverted())
                                <span class="text-xs text-green-600">✓ Has profile</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $lead->phone ?: $lead->email ?: '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 capitalize">{{ $lead->source }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ match($lead->status) {
                                        'new' => 'bg-blue-100 text-blue-800',
                                        'contacted' => 'bg-amber-100 text-amber-800',
                                        'interested' => 'bg-purple-100 text-purple-800',
                                        'registered' => 'bg-green-100 text-green-800',
                                        'not_interested', 'closed' => 'bg-gray-100 text-gray-600',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }}">
                                    {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $lead->assignedTo?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs {{ $lead->next_follow_up_at && $lead->next_follow_up_at->isPast() ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                {{ $lead->next_follow_up_at?->format('d M Y') ?? '—' }}
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
