<x-matchmaker-layout>
    <x-slot name="header">
        <span class="text-gray-700 font-semibold">{{ __('db.My Commissions') }}</span>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        @if ($application)
        <div class="rounded-xl p-4 flex items-center gap-3" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
            <span class="text-2xl">🎖️</span>
            <div>
                <p class="text-white font-semibold">{{ \App\Models\MatchmakerApplication::LEVELS[$application->level] }}</p>
                <p class="text-white/60 text-xs">{{ __('db.ID: :code', ['code' => $application->counselor_code]) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex flex-wrap justify-between items-center gap-3 mb-2">
                <h4 class="font-semibold text-gray-700">🔗 {{ __('db.My Referral Link') }}</h4>
                <p class="text-sm"><strong style="color: #0d6b6b">{{ $referralCount }}</strong> <span class="text-gray-500">{{ __('db.registered through it') }}</span></p>
            </div>
            <p class="text-xs text-gray-500 mb-3">{{ __('db.Share this on your visiting card, WhatsApp, or social media — anyone who registers through it is credited to you, including if they later verify their profile.') }}</p>
            <div class="flex flex-wrap items-start gap-4">
                <div class="flex-1 min-w-[16rem] space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <input type="text" readonly value="{{ $referralLink }}" class="text-xs border-gray-200 rounded-lg flex-1 min-w-[14rem] bg-gray-50" onclick="this.select()" id="referral-link">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('referral-link').value); this.textContent = {{ Js::from(__('db.Copied!')) }}; setTimeout(() => this.textContent = {{ Js::from(__('db.Copy Link')) }}, 1500);" class="text-xs font-semibold px-2 py-1.5 rounded-lg text-white hover:opacity-90" style="background: #0d6b6b">{{ __('db.Copy Link') }}</button>
                    </div>
                </div>
                @if ($referralQrCode)
                <div class="text-center flex-shrink-0">
                    <img src="{{ $referralQrCode }}" class="w-24 h-24 rounded-lg border border-gray-200 p-1">
                    <p class="text-xs text-gray-400 mt-1">{{ __('db.Scan to register') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="grid sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('db.Pending') }}</p>
                <p class="text-2xl font-bold text-amber-600">Rs. {{ number_format($totals['pending'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('db.Under 7-day review hold') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('db.Approved') }}</p>
                <p class="text-2xl font-bold" style="color: #0d6b6b">Rs. {{ number_format($totals['approved'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('db.Ready to be paid to you') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('db.Paid') }}</p>
                <p class="text-2xl font-bold text-green-700">Rs. {{ number_format($totals['paid'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('db.Total received so far') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm divide-y">
            @forelse ($entries as $entry)
            <div class="p-5 flex flex-wrap justify-between items-start gap-3">
                <div>
                    <p class="font-medium text-gray-800">{{ match($entry->rule_type) {
                        'verified_profile' => __('db.Verified Profile Commission'),
                        'recognition_bonus' => '🏆 ' . __('db.Recognition Bonus'),
                        default => __('db.:package Commission (:type)', ['package' => $entry->nikahPackage?->name ?? __('db.Package'), 'type' => $entry->is_renewal ? __('db.Renewal') : __('db.First Purchase')]),
                    } }}</p>
                    @if ($entry->notes)
                    <p class="text-xs text-gray-500 mt-1 italic">"{{ $entry->notes }}"</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">{{ __('db.Earned :date', ['date' => $entry->created_at->format('d M Y')]) }}</p>
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
            <p class="p-5 text-gray-500">{{ __("db.No commissions earned yet — they'll show up here automatically as your clients' verified profiles and packages get confirmed.") }}</p>
            @endforelse
        </div>

        {{ $entries->links() }}
    </div>
</x-matchmaker-layout>
