@php
$tierColors = ['nikah_counselor' => '#0d6b6b', 'certified_nikah_counselor' => '#1a6fb8', 'senior_nikah_counselor' => '#b8962e', 'regional_nikah_coordinator' => '#7a2e8c'];
$tierBadges = ['nikah_counselor' => '🥉', 'certified_nikah_counselor' => '🥈', 'senior_nikah_counselor' => '🥇', 'regional_nikah_coordinator' => '⭐'];
$myTier = $application->level ?? 'nikah_counselor';
$accent = $tierColors[$myTier];
@endphp
<x-matchmaker-layout>
    <x-slot name="header">
        <span class="text-gray-700 font-semibold">My Performance</span>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Tier hero --}}
        <div class="rounded-xl p-6 flex flex-wrap items-center justify-between gap-4" style="background: linear-gradient(135deg, {{ $accent }} 0%, #1a1a2e 100%);">
            <div class="flex items-center gap-4">
                <span class="text-5xl">{{ $tierBadges[$myTier] }}</span>
                <div>
                    <p class="text-white/60 text-xs uppercase tracking-wide">Your Current Level</p>
                    <p class="text-white text-2xl font-bold">{{ \App\Models\MatchmakerApplication::LEVELS[$myTier] }}</p>
                    @if ($application)
                    <p class="text-white/60 text-xs">ID: {{ $application->counselor_code }}</p>
                    @endif
                </div>
            </div>
            @if ($score['overall'] !== null)
            <div class="text-center">
                <p class="text-white/60 text-xs uppercase tracking-wide">Quality Score</p>
                <p class="text-white text-4xl font-extrabold">{{ $score['overall'] }}%</p>
            </div>
            @endif
        </div>

        {{-- Stats grid --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="font-semibold text-gray-700 mb-4 border-b pb-2">📈 This Is What You've Built</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                @foreach ([
                ['👥', $stats['introduced'], 'Profiles Introduced'],
                ['✅', $stats['verified'], 'Verified'],
                ['💳', $stats['paid'], 'Paid & Confirmed'],
                ['💼', $stats['matchmaking_clients'], 'Matchmaking Clients'],
                ['💌', $stats['proposals'], 'Proposals Sent'],
                ['💞', $stats['mutual_interests'], 'Mutual Interests'],
                ['🏆', $stats['recognition_bonuses'], 'Success Awards'],
                ['🔗', $stats['referrals'], 'Referred Registrations'],
                ] as $stat)
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-2xl">{{ $stat[0] }}</p>
                    <p class="text-xl font-bold text-gray-800 mt-1">{{ $stat[1] }}</p>
                    <p class="text-xs text-gray-500">{{ $stat[2] }}</p>
                </div>
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Total Commission Earned</p>
                <p class="text-2xl font-bold" style="color: {{ $accent }}">Rs. {{ number_format($commissionEarned, 2) }}</p>
            </div>
        </div>

        {{-- Quality score breakdown --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="font-semibold text-gray-700 mb-1 border-b pb-2">🎯 Quality Score Breakdown</h4>
            <p class="text-xs text-gray-500 mt-2 mb-4">A simplified score built from what Sallaamti can measure today. Client Satisfaction, Proposal Quality, and Follow-up Response tracking will be added as those systems are built — this score will grow more complete over time, not less accurate.</p>

            @if ($score['overall'] === null)
            <p class="text-sm text-gray-400 text-center py-6">Introduce your first client to start building your Quality Score.</p>
            @else
            <div class="space-y-4">
                @foreach ([
                ['Verification Rate', $score['verification_rate'], 'How many of the people you\'ve introduced went on to get verified.'],
                ['Paid Conversion', $score['paid_conversion_rate'], 'How many of the people you\'ve introduced completed payment.'],
                ['Compliance', $score['compliance_rate'], 'How clean your commission history is — no flagged entries.'],
                ] as $factor)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $factor[0] }}</span>
                        <span class="font-semibold text-gray-800">{{ $factor[1] }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full" style="width: {{ $factor[1] }}%; background: {{ $accent }}"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $factor[2] }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Level progression --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="font-semibold text-gray-700 mb-4 border-b pb-2">🪜 Levels</h4>
            <div class="grid sm:grid-cols-4 gap-3">
                @foreach (\App\Models\MatchmakerApplication::LEVELS as $value => $label)
                <div class="rounded-lg p-4 text-center {{ $value === $myTier ? 'ring-2' : 'bg-gray-50' }}" @if($value === $myTier) style="background: {{ $tierColors[$value] }}11; ring-color: {{ $tierColors[$value] }}" @endif>
                    <p class="text-2xl">{{ $tierBadges[$value] }}</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1">{{ $label }}</p>
                    @if ($value === $myTier)
                    <p class="text-xs font-semibold mt-1" style="color: {{ $tierColors[$value] }}">You are here</p>
                    @endif
                </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-4 text-center">Ask your Sallaamti coordinator about what it takes to move up a level — higher tiers earn a higher commission rate on every sale.</p>
        </div>

    </div>
</x-matchmaker-layout>
