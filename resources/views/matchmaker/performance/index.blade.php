@php
$tierColors = ['nikah_counselor' => '#0d6b6b', 'certified_nikah_counselor' => '#1a6fb8', 'senior_nikah_counselor' => '#b8962e', 'regional_nikah_coordinator' => '#7a2e8c'];
$tierBadges = ['nikah_counselor' => '🥉', 'certified_nikah_counselor' => '🥈', 'senior_nikah_counselor' => '🥇', 'regional_nikah_coordinator' => '⭐'];
$myTier = $application->level ?? 'nikah_counselor';
$accent = $tierColors[$myTier];
@endphp
<x-matchmaker-layout>
    <x-slot name="header">
        <span class="text-gray-700 font-semibold">{{ __('db.My Performance') }}</span>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Tier hero --}}
        <div class="rounded-xl p-6 flex flex-wrap items-center justify-between gap-4" style="background: linear-gradient(135deg, {{ $accent }} 0%, #1a1a2e 100%);">
            <div class="flex items-center gap-4">
                <span class="text-5xl">{{ $tierBadges[$myTier] }}</span>
                <div>
                    <p class="text-white/60 text-xs uppercase tracking-wide">{{ __('db.Your Current Level') }}</p>
                    <p class="text-white text-2xl font-bold">{{ \App\Models\MatchmakerApplication::LEVELS[$myTier] }}</p>
                    @if ($application)
                    <p class="text-white/60 text-xs">{{ __('db.ID: :code', ['code' => $application->counselor_code]) }}</p>
                    @endif
                </div>
            </div>
            @if ($score['overall'] !== null)
            <div class="text-center">
                <p class="text-white/60 text-xs uppercase tracking-wide">{{ __('db.Quality Score') }}</p>
                <p class="text-white text-4xl font-extrabold">{{ $score['overall'] }}%</p>
            </div>
            @endif
        </div>

        {{-- Stats grid --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="font-semibold text-gray-700 mb-4 border-b pb-2">📈 {{ __("db.This Is What You've Built") }}</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                @foreach ([
                ['👥', $stats['introduced'], __('db.Profiles Introduced')],
                ['✅', $stats['verified'], __('db.Verified')],
                ['💳', $stats['paid'], __('db.Paid & Confirmed')],
                ['💼', $stats['matchmaking_clients'], __('db.Matchmaking Clients')],
                ['💌', $stats['proposals'], __('db.Proposals Sent')],
                ['💞', $stats['mutual_interests'], __('db.Mutual Interests')],
                ['🏆', $stats['recognition_bonuses'], __('db.Success Awards')],
                ['🔗', $stats['referrals'], __('db.Referred Registrations')],
                ] as $stat)
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-2xl">{{ $stat[0] }}</p>
                    <p class="text-xl font-bold text-gray-800 mt-1">{{ $stat[1] }}</p>
                    <p class="text-xs text-gray-500">{{ $stat[2] }}</p>
                </div>
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('db.Total Commission Earned') }}</p>
                <p class="text-2xl font-bold" style="color: {{ $accent }}">Rs. {{ number_format($commissionEarned, 2) }}</p>
            </div>
        </div>

        {{-- Quality score breakdown --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="font-semibold text-gray-700 mb-1 border-b pb-2">🎯 {{ __('db.Quality Score Breakdown') }}</h4>
            <p class="text-xs text-gray-500 mt-2 mb-4">{{ __('db.A simplified score built from what Sallaamti can measure today. Client Satisfaction, Proposal Quality, and Follow-up Response tracking will be added as those systems are built — this score will grow more complete over time, not less accurate.') }}</p>

            @if ($score['overall'] === null)
            <p class="text-sm text-gray-400 text-center py-6">{{ __('db.Introduce your first client to start building your Quality Score.') }}</p>
            @else
            <div class="space-y-4">
                @foreach ([
                [__('db.Verification Rate'), $score['verification_rate'], __("db.How many of the people you've introduced went on to get verified.")],
                [__('db.Paid Conversion'), $score['paid_conversion_rate'], __("db.How many of the people you've introduced completed payment.")],
                [__('db.Compliance'), $score['compliance_rate'], __('db.How clean your commission history is — no flagged entries.')],
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
            <h4 class="font-semibold text-gray-700 mb-4 border-b pb-2">🪜 {{ __('db.Levels') }}</h4>
            <div class="grid sm:grid-cols-4 gap-3">
                @foreach (\App\Models\MatchmakerApplication::LEVELS as $value => $label)
                <div class="rounded-lg p-4 text-center {{ $value === $myTier ? 'ring-2' : 'bg-gray-50' }}" @if($value === $myTier) style="background: {{ $tierColors[$value] }}11; ring-color: {{ $tierColors[$value] }}" @endif>
                    <p class="text-2xl">{{ $tierBadges[$value] }}</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1">{{ $label }}</p>
                    @if ($value === $myTier)
                    <p class="text-xs font-semibold mt-1" style="color: {{ $tierColors[$value] }}">{{ __('db.You are here') }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @if ($levelProgress)
            <div class="mt-6 pt-6 border-t">
                <p class="text-sm font-semibold text-gray-700 mb-1">🚀 {{ __('db.On Your Way to :level', ['level' => $levelProgress['next_level_label']]) }}</p>
                <p class="text-xs text-gray-400 mb-4">{{ __("db.Meet all three below and you're promoted automatically — no need to ask anyone. Higher tiers earn a higher commission rate on every sale.") }}</p>
                <div class="space-y-3">
                    @foreach ([
                        [__('db.Verified Profiles'), $levelProgress['verified'], ''],
                        [__('db.Quality Score'), $levelProgress['quality_score'], '%'],
                        [__('db.Days as a Counselor'), $levelProgress['tenure_days'], ' ' . __('db.days')],
                    ] as $req)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $req[0] }}</span>
                            <span class="font-semibold {{ $req[1]['met'] ? 'text-green-600' : 'text-gray-800' }}">
                                {{ $req[1]['met'] ? '✓ ' : '' }}{{ $req[1]['current'] }}{{ $req[2] }} / {{ $req[1]['needed'] }}{{ $req[2] }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $req[1]['met'] ? 'bg-green-500' : '' }}" style="width: {{ min(100, $req[1]['needed'] > 0 ? round($req[1]['current'] / $req[1]['needed'] * 100) : 100) }}%; {{ $req[1]['met'] ? '' : 'background: ' . $accent }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <p class="text-xs text-gray-400 mt-4 text-center">{{ __("db.You've reached the highest level — thank you for your outstanding work.") }}</p>
            @endif
        </div>

    </div>
</x-matchmaker-layout>
