{{-- Single Check Profiles card — reused for the initial server render and every
     infinite-scroll batch fetched by browse.blade.php's JS, so the two never drift.

     The whole card is clickable through to the full profile via a single
     full-card link sitting UNDER everything (absolute inset-0, z-0).
     Anything that must stay independently clickable (the "more" menu, the
     match-breakdown toggle, the action buttons) is explicitly lifted above
     it with `relative z-10`, since a positioned element always paints above
     static content regardless of DOM order — without that, either the link
     would swallow every click, or the buttons would swallow the link. --}}
@php $badges = $profile->trustBadges(); @endphp
<div class="group relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition duration-300 hover:shadow-xl hover:-translate-y-1">

    <a href="{{ route('nikah.profile.view', $profile) }}" class="absolute inset-0 z-0" aria-label="{{ __('db.View full profile') }}"></a>

    {{-- Photo --}}
    <div class="relative h-52 bg-gradient-to-br from-teal-50 to-gray-100 flex items-center justify-center overflow-hidden">
        @if ($profile->photos->first())
        <img src="{{ route('nikah.photos.show', $profile->photos->first()) }}" alt="{{ __('db.Profile photo (blurred until you connect)') }}" class="w-full h-full object-cover blur-md scale-105 group-hover:scale-110 transition duration-500">
        <div class="absolute inset-0 flex items-center justify-center text-white text-sm font-medium bg-black/10">
            <span class="bg-black/40 backdrop-blur-sm px-3 py-1 rounded-full">🔒 {{ __('db.Photo hidden') }}</span>
        </div>
        @else
        <div class="text-6xl opacity-30">👤</div>
        @endif

        {{-- Soft gradient at the bottom so badges/text always stay legible over any photo --}}
        <div class="absolute inset-x-0 bottom-0 h-14 bg-gradient-to-t from-black/40 to-transparent"></div>

        {{-- "More" menu — badges on the start (left) side, this on the end (right) --}}
        <details class="absolute top-2 end-2 z-10">
            <summary class="w-7 h-7 flex items-center justify-center rounded-full bg-white/90 backdrop-blur text-gray-500 cursor-pointer hover:bg-white shadow list-none text-sm">⋯</summary>
            <div class="absolute end-0 mt-1 bg-white border rounded-lg shadow-lg z-10 w-36 overflow-hidden">
                <form method="POST" action="{{ route('nikah.block', $profile) }}">
                    @csrf
                    <button class="w-full text-left text-xs px-3 py-2 hover:bg-gray-50 text-gray-600">🚫 {{ __('db.Block') }}</button>
                </form>
                <button onclick="document.getElementById('report-{{ $profile->id }}').classList.toggle('hidden')" class="w-full text-left text-xs px-3 py-2 hover:bg-gray-50 text-red-500">⚑ {{ __('db.Report') }}</button>
            </div>
        </details>

        {{-- Trust badge stack — independently-earned signals instead of one binary "Verified" --}}
        <div class="absolute top-2 start-2 flex flex-col gap-1 items-start">
            @if ($badges['cnic'])
            <span class="text-[11px] font-semibold bg-emerald-500 text-white px-2 py-0.5 rounded-full shadow flex items-center gap-1" title="{{ __('db.CNIC verified by our team') }}">🪪 {{ __('db.CNIC Verified') }}</span>
            @endif
            @if ($badges['payment'])
            <span class="text-[11px] font-semibold bg-blue-500 text-white px-2 py-0.5 rounded-full shadow flex items-center gap-1" title="{{ __('db.Verification fee payment confirmed') }}">💳 {{ __('db.Payment Verified') }}</span>
            @endif
            @if ($badges['guardian'])
            <span class="text-[11px] font-semibold bg-purple-500 text-white px-2 py-0.5 rounded-full shadow flex items-center gap-1" title="{{ __('db.Guardian contact confirmed by our team') }}">👨‍👩‍👦 {{ __('db.Guardian Verified') }}</span>
            @endif
            @if (!$badges['cnic'] && !$badges['payment'] && !$badges['guardian'])
            <span class="text-[11px] font-semibold bg-gray-500/90 text-white px-2 py-0.5 rounded-full shadow flex items-center gap-1" title="{{ __(\"db.Our team hasn't finished reviewing this profile yet\") }}">⏳ {{ __('db.Verification Pending') }}</span>
            @endif
        </div>

        {{-- New badge --}}
        @if ($profile->created_at->gt(now()->subDays(14)))
        <span class="absolute bottom-2 start-2 text-[11px] font-bold text-white px-2 py-0.5 rounded-full shadow" style="background: var(--gold)">✨ {{ __('db.New') }}</span>
        @endif

        {{-- Match % badge --}}
        @if ($profile->match_percentage > 0)
        <div class="absolute bottom-2 end-2 rounded-full px-2.5 py-1 text-xs font-bold shadow text-white
                    {{ $profile->match_percentage >= 80 ? 'bg-emerald-500' :
                       ($profile->match_percentage >= 50 ? 'bg-amber-500' : 'bg-gray-500') }}">
            {{ __('db.:percent% match', ['percent' => $profile->match_percentage]) }}
        </div>
        @endif
    </div>

    <div class="p-4">
        {{-- Match bar --}}
        @if ($profile->match_percentage > 0)
        <div class="mb-3">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>{{ __('db.Match Score') }}</span>
                <span>{{ $profile->match_percentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="h-1.5 rounded-full
                            {{ $profile->match_percentage >= 80 ? 'bg-emerald-500' :
                               ($profile->match_percentage >= 50 ? 'bg-amber-500' : 'bg-gray-400') }}"
                    style="width: {{ $profile->match_percentage }}%"></div>
            </div>
            @if (!empty($profile->match_criteria))
            <details class="relative z-10 mt-1">
                <summary class="text-xs font-medium cursor-pointer" style="color: var(--teal)">{{ __('db.Why :percent%?', ['percent' => $profile->match_percentage]) }}</summary>
                <ul class="mt-1 space-y-0.5">
                    @foreach ($profile->match_criteria as $c)
                    <li class="text-xs {{ $c['matched'] ? 'text-emerald-600' : 'text-gray-400' }}">
                        {{ $c['matched'] ? '✓' : '✗' }} {{ $c['label'] }}
                    </li>
                    @endforeach
                </ul>
            </details>
            @endif
        </div>
        @endif

        <div>
            <div class="flex items-center gap-1.5">
                <h4 class="font-bold text-gray-800 text-lg group-hover:text-[--teal] transition">{{ __('db.:age yrs · :city', ['age' => $profile->age, 'city' => $profile->city]) }}</h4>
                @if ($profile->user?->gender)
                <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded-full {{ $profile->user->gender === 'female' ? 'bg-pink-50 text-pink-500' : 'bg-blue-50 text-blue-500' }}">
                    {{ $profile->user->gender === 'female' ? '♀ ' . __('db.Female') : '♂ ' . __('db.Male') }}
                </span>
                @endif
            </div>
            <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1">
                @if ($profile->profession)<span class="text-xs text-gray-600 flex items-center gap-1">💼 {{ $profile->profession }}</span>@endif
                @if ($profile->education)<span class="text-xs text-gray-600 flex items-center gap-1">🎓 {{ $profile->education }}</span>@endif
                @if ($profile->sect)<span class="text-xs text-gray-600 flex items-center gap-1">☪️ {{ $profile->sect }}</span>@endif
                @if ($profile->ethnicity)<span class="text-xs text-gray-600 flex items-center gap-1">🌍 {{ $profile->ethnicity }}</span>@endif
            </div>
            <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">{{ ucfirst(str_replace('_', ' ', $profile->marital_status)) }}</span>
                <span class="text-xs text-gray-400">
                    @if ($profile->last_active_at && $profile->last_active_at->gt(now()->subDays(7)))
                    <span class="text-emerald-600 font-medium">● {{ __('db.Active :time ago', ['time' => $profile->last_active_at->diffForHumans(null, true)]) }}</span>
                    @else
                    {{ __('db.Member since :date', ['date' => $profile->created_at->format('M Y')]) }}
                    @endif
                </span>
            </div>
            <p class="mt-2 text-xs font-semibold" style="color: var(--teal)">{{ __('db.View Full Profile') }} →</p>
        </div>

        <div class="relative z-10 mt-3 flex gap-2">
            {{-- Express Interest --}}
            @if (in_array($profile->id, $sentInterestIds))
            <button disabled class="flex-1 bg-gray-100 text-gray-400 text-sm font-medium py-2 rounded-lg">{{ __('db.Sent') }} ✓</button>
            @else
            <form method="POST" action="{{ route('nikah.interest.send', $profile) }}" class="flex-1">
                @csrf
                <button class="w-full text-white text-sm font-semibold py-2 rounded-lg shadow-sm transition hover:shadow-md hover:brightness-110" style="background: linear-gradient(135deg, #e11d78, #be185d)">💌 {{ __('db.Express Interest') }}</button>
            </form>
            @endif

            {{-- Save/Bookmark --}}
            <form method="POST" action="{{ route('nikah.save', $profile) }}">
                @csrf
                <button class="px-3 py-2 rounded-lg border transition {{ in_array($profile->id, $savedProfileIds) ? 'bg-amber-50 border-amber-300 text-amber-500' : 'border-gray-200 text-gray-300 hover:border-gray-300 hover:text-gray-400' }}">
                    {{ in_array($profile->id, $savedProfileIds) ? '★' : '☆' }}
                </button>
            </form>
        </div>

        <div id="report-{{ $profile->id }}" class="relative z-10 hidden mt-2">
            <form method="POST" action="{{ route('nikah.report', $profile) }}" class="text-xs space-y-1">
                @csrf
                <input type="text" name="reason" placeholder="{{ __('db.Reason') }}" class="border rounded w-full px-2 py-1 text-xs" required>
                <button class="bg-red-600 text-white px-2 py-1 rounded text-xs w-full">{{ __('db.Submit Report') }}</button>
            </form>
        </div>
    </div>
</div>
