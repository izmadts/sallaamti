<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('matchmaker.nikah.index') }}" class="text-gray-400 hover:text-gray-600">{{ __('db.Nikah Profiles') }}</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ __('db.:age yrs, :gender', ['age' => $profile->age, 'gender' => ucfirst($profile->user?->gender ?? '—')]) }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ __('db.:age yrs, :gender', ['age' => $profile->age, 'gender' => ucfirst($profile->user?->gender ?? '—')]) }}</h2>
                        <p class="text-sm text-gray-500">{{ $profile->city }}{{ $profile->country ? ', ' . $profile->country : '' }}</p>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full font-semibold {{ $profile->verification_status === 'verified' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($profile->verification_status) }}
                    </span>
                </div>

                @if ($profile->isWalkIn())
                <p class="text-xs text-amber-700 mb-4">🚶 {{ __('db.Walk-in — entered by :name', ['name' => $profile->createdBy?->name ?? __('db.a staff member (account since removed)')]) }}</p>
                @endif

                <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    <div><dt class="text-gray-400">{{ __('db.Height') }}</dt><dd>{{ $profile->height ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Marital Status') }}</dt><dd>{{ ucfirst(str_replace('_', ' ', $profile->marital_status ?? '—')) }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Sect') }}</dt><dd>{{ $profile->sect ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Caste') }}</dt><dd>{{ $profile->caste ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Education') }}</dt><dd>{{ $profile->education ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Profession') }}</dt><dd>{{ $profile->profession ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Family Type') }}</dt><dd>{{ $profile->family_type ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Ethnicity') }}</dt><dd>{{ $profile->ethnicity ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Language') }}</dt><dd>{{ $profile->language ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Open to Polygamy') }}</dt><dd>{{ $profile->open_to_polygamy ? __('db.Yes') : __('db.No') }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Prayer Regularity') }}</dt><dd>{{ $profile->prayer_frequency ? ucfirst($profile->prayer_frequency) : '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Hijab/Beard') }}</dt><dd>{{ $profile->hijab_or_beard ? ucfirst($profile->hijab_or_beard) : '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Smoking') }}</dt><dd>{{ $profile->smokes ? ucfirst($profile->smokes) : '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Diet') }}</dt><dd>{{ $profile->diet ? ucfirst(str_replace('_', ' ', $profile->diet)) : '—' }}</dd></div>
                </dl>
            </div>

            @if ($canActOnBehalf)
            <div class="bg-white rounded-xl shadow-sm p-6">
                @include('matchmaker.nikah._payment-form')
            </div>
            @endif

            @if ($pendingReceivedInterests->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6">
                @include('matchmaker.nikah._interest-inbox', ['interests' => $pendingReceivedInterests])
            </div>
            @endif

            @if ($linkedLead && $linkedLead->progress_link_token)
            <div class="rounded-xl p-4 bg-blue-50 border border-blue-200 text-sm text-blue-800">
                🔗 {{ __('db.This profile has a linked client record with a secure progress & documents link.') }}
                <a href="{{ route('matchmaker.clients.show', $linkedLead) }}" class="font-semibold hover:underline">{{ __('db.View & copy it') }} →</a>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">{{ __('db.About') }}</h4>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $profile->about ?: __('db.— not filled in —') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">{{ __('db.Looking For') }}</h4>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $profile->expectations ?: __('db.— not filled in —') }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">{{ __('db.Partner Preferences') }}</h4>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    <div><dt class="text-gray-400">{{ __('db.Age Range') }}</dt><dd>{{ $profile->pref_min_age ?: '—' }} – {{ $profile->pref_max_age ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.City') }}</dt><dd>{{ $profile->pref_city ?: __('db.Any') }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Sect') }}</dt><dd>{{ $profile->pref_sect ?: __('db.Any') }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Education') }}</dt><dd>{{ $profile->pref_education ?: __('db.Any') }}</dd></div>
                    <div><dt class="text-gray-400">{{ __('db.Marital Status') }}</dt><dd>{{ $profile->pref_marital_status ?: __('db.Any') }}</dd></div>
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">{{ __('db.Guardian') }}</h4>
                <dl class="text-sm space-y-2 mb-4">
                    <div class="flex justify-between"><dt class="text-gray-400">{{ __('db.Name') }}</dt><dd>{{ $profile->guardian_name ?: '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">{{ __('db.Relation') }}</dt><dd>{{ $profile->guardian_relation ?: '—' }}</dd></div>
                </dl>

                @if ($existingRequest && $existingRequest->status === 'approved')
                <div class="rounded-lg p-4" style="background: #f0fdf4; border: 1px solid #86efac">
                    <p class="text-xs font-semibold text-green-700 mb-1">✅ {{ __('db.Contact details approved') }}</p>
                    <p class="text-sm text-gray-700">{{ __('db.Guardian Contact:') }} <strong>{{ $profile->guardian_contact ?: '—' }}</strong></p>
                </div>
                @elseif ($existingRequest && $existingRequest->status === 'pending')
                <div class="rounded-lg p-3 text-sm text-yellow-700" style="background: #fefce8; border: 1px solid #fde68a">
                    ⏳ {{ __('db.Your request is awaiting admin review.') }}
                </div>
                @elseif ($existingRequest && $existingRequest->status === 'denied')
                <div class="rounded-lg p-3 text-sm text-red-700 mb-3" style="background: #fef2f2; border: 1px solid #fecaca">
                    ❌ {{ __('db.Your previous request was denied.') }}
                    @if ($existingRequest->admin_notes)
                    <p class="mt-1 italic">"{{ $existingRequest->admin_notes }}"</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('matchmaker.nikah.request-contact', $profile) }}">
                    @csrf
                    <button class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">{{ __('db.Request Contact Details Again') }}</button>
                </form>
                @else
                <div class="rounded-lg p-4 flex items-center justify-between" style="background: var(--cream)">
                    <p class="text-sm text-gray-500">🔒 {{ __('db.Contact details are hidden') }}</p>
                    <form method="POST" action="{{ route('matchmaker.nikah.request-contact', $profile) }}">
                        @csrf
                        <button class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">{{ __('db.Request Contact Details') }}</button>
                    </form>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
