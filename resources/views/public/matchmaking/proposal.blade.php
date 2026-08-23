<x-guest-layout title="A Proposed Match — Sallaamti" description="Your matchmaker has shared a proposed match for you to review.">

    <div class="py-12 bg-cream">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm text-center">{{ session('status') }}</div>
            @endif

            <div class="rounded-xl p-4 flex items-start gap-3 bg-white border" style="border-color: #0d6b6b33">
                <span class="text-xl">💍</span>
                <p class="text-sm text-gray-700">
                    Your matchmaker has shared this proposed match for you to review. Only your response is recorded here — no account or password needed.
                </p>
            </div>

            @if ($proposal->batch->nikahProfile)
            <p class="text-center text-sm text-gray-500">A proposed match for <strong>{{ $proposal->batch->nikahProfile->user?->name }}</strong></p>
            @endif

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="h-40 bg-gradient-to-br {{ $proposal->candidate->user?->gender === 'female' ? 'from-pink-100 to-rose-200' : 'from-blue-100 to-indigo-200' }} relative flex items-center justify-center">
                    <div class="w-28 h-28 rounded-full bg-white shadow-lg flex items-center justify-center text-4xl border-4 border-white">
                        {{ $proposal->candidate->user?->gender === 'female' ? '👩' : '👨' }}
                    </div>
                    <div class="absolute top-2 left-1">
                        <span class="flex items-center gap-1 bg-green-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow">
                            ✅ Verified Profile
                        </span>
                    </div>
                </div>

                <div class="p-6 text-center">
                    <h3 class="text-xl font-bold text-gray-800">
                        {{ $proposal->candidate->age }} years, {{ $proposal->candidate->city }}
                        @if ($proposal->candidate->country && $proposal->candidate->country !== 'Pakistan') · {{ $proposal->candidate->country }} @endif
                    </h3>
                    <p class="text-gray-500 mt-1">
                        {{ ucfirst(str_replace('_', ' ', $proposal->candidate->marital_status)) }}
                        @if ($proposal->candidate->sect) · {{ $proposal->candidate->sect }} @endif
                    </p>
                </div>
            </div>

            @if ($proposal->match_reasons)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Why your matchmaker suggested this</h4>
                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                    @foreach ($proposal->match_reasons as $reason)
                    @if (trim($reason) !== '')
                    <li>{{ $reason }}</li>
                    @endif
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Details</h4>
                <dl class="text-sm space-y-2">
                    @if ($proposal->candidate->height)
                    <div class="flex justify-between"><dt class="text-gray-500">Height</dt><dd>{{ $proposal->candidate->height }}</dd></div>
                    @endif
                    @if ($proposal->candidate->education)
                    <div class="flex justify-between"><dt class="text-gray-500">Education</dt><dd>{{ $proposal->candidate->education }}</dd></div>
                    @endif
                    @if ($proposal->candidate->profession)
                    <div class="flex justify-between"><dt class="text-gray-500">Profession</dt><dd>{{ $proposal->candidate->profession }}</dd></div>
                    @endif
                    @if ($proposal->candidate->family_type)
                    <div class="flex justify-between"><dt class="text-gray-500">Family Type</dt><dd>{{ $proposal->candidate->family_type }}</dd></div>
                    @endif
                </dl>

                @if ($proposal->candidate->about)
                <div class="mt-4 pt-4 border-t">
                    <h5 class="font-semibold text-gray-700 mb-2 text-sm">About</h5>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $proposal->candidate->about }}</p>
                </div>
                @endif
            </div>

            {{-- Response --}}
            <div class="rounded-xl p-6 text-center" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
                @if ($proposal->status === 'responded')
                <p class="text-white font-semibold mb-1">Thank you — you already responded to this proposal.</p>
                <p class="text-white/80 text-sm">
                    Your response: <strong>{{ ucfirst(str_replace('_', ' ', $proposal->response)) }}</strong>
                </p>
                <p class="text-white/70 text-xs mt-3">Your matchmaker has been notified and will be in touch about next steps.</p>
                @else
                <p class="text-white font-semibold mb-4">What do you think of this match?</p>
                <form method="POST" action="{{ $respondUrl }}" class="flex flex-wrap justify-center gap-3">
                    @csrf
                    <button name="response" value="interested" class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-5 py-3 rounded-lg transition hover:-translate-y-0.5">👍 Interested</button>
                    <button name="response" value="maybe" class="bg-amber-400 hover:bg-amber-500 text-white text-sm font-semibold px-5 py-3 rounded-lg transition hover:-translate-y-0.5">🤔 Maybe / Need More Info</button>
                    <button name="response" value="not_interested" class="bg-white/20 hover:bg-white/30 text-white text-sm font-semibold px-5 py-3 rounded-lg transition hover:-translate-y-0.5">🙏 Not Interested</button>
                </form>
                <p class="text-white/60 text-xs mt-4">Your matchmaker will follow up with you directly after you respond.</p>
                @endif
            </div>

        </div>
    </div>
</x-guest-layout>
