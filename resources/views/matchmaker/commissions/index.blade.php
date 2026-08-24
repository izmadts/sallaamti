<x-matchmaker-layout>
    <x-slot name="header">
        <span class="text-gray-700 font-semibold">My Commissions</span>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        @if ($application)
        <div class="rounded-xl p-4 flex items-center gap-3" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
            <span class="text-2xl">🎖️</span>
            <div>
                <p class="text-white font-semibold">{{ \App\Models\MatchmakerApplication::LEVELS[$application->level] }}</p>
                <p class="text-white/60 text-xs">ID: {{ $application->counselor_code }}</p>
            </div>
        </div>
        @endif

        <div class="grid sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Pending</p>
                <p class="text-2xl font-bold text-amber-600">Rs. {{ number_format($totals['pending'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">Under 7-day review hold</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Approved</p>
                <p class="text-2xl font-bold" style="color: #0d6b6b">Rs. {{ number_format($totals['approved'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">Ready to be paid to you</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Paid</p>
                <p class="text-2xl font-bold text-green-700">Rs. {{ number_format($totals['paid'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">Total received so far</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm divide-y">
            @forelse ($entries as $entry)
            <div class="p-5 flex flex-wrap justify-between items-start gap-3">
                <div>
                    <p class="font-medium text-gray-800">{{ match($entry->rule_type) {
                        'verified_profile' => 'Verified Profile Commission',
                        'recognition_bonus' => '🏆 Recognition Bonus',
                        default => ($entry->nikahPackage?->name ?? 'Package') . ' Commission (' . ($entry->is_renewal ? 'Renewal' : 'First Purchase') . ')',
                    } }}</p>
                    @if ($entry->notes)
                    <p class="text-xs text-gray-500 mt-1 italic">"{{ $entry->notes }}"</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">Earned {{ $entry->created_at->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-800">Rs. {{ number_format($entry->commission_amount, 2) }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                        {{ match($entry->status) {
                            'paid' => 'bg-green-100 text-green-800',
                            'approved' => 'bg-blue-100 text-blue-800',
                            default => 'bg-amber-100 text-amber-800',
                        } }}">
                        {{ ucfirst($entry->status) }}
                    </span>
                </div>
            </div>
            @empty
            <p class="p-5 text-gray-500">No commissions earned yet — they'll show up here automatically as your clients' verified profiles and packages get confirmed.</p>
            @endforelse
        </div>

        {{ $entries->links() }}
    </div>
</x-matchmaker-layout>
