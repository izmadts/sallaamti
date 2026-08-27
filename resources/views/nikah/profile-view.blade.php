<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('db.Profile Details') }}</h2>
            <a href="{{ route('nikah.browse') }}" class="text-sm text-gray-500 hover:underline">← {{ __('db.Back to Profiles') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">


            {{-- Header Card --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">

                {{-- Photo Banner --}}
                <div class="h-48 bg-gradient-to-br {{ $profile->user?->gender === 'female' ? 'from-pink-100 to-rose-200' : 'from-blue-100 to-indigo-200' }} relative flex items-center justify-center">
                    @if ($hasAcceptedInterest && $profile->photos->isNotEmpty())
                    <img src="{{ route('nikah.photos.show', $profile->photos->first()) }}"
                        alt="{{ __('db.:age yrs, :city', ['age' => $profile->age, 'city' => $profile->city]) }}"
                        class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                    @else
                    <div class="w-32 h-32 rounded-full bg-white shadow-lg flex items-center justify-center text-5xl border-4 border-white">
                        {{ $profile->user?->gender === 'female' ? '👩' : '👨' }}
                    </div>
                    @if ($profile->photos->isNotEmpty())
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2">
                        <span class="bg-white/80 text-gray-600 text-xs px-3 py-1 rounded-full">
                            🔒 {{ __('db.:count photo(s) — visible after acceptance', ['count' => $profile->photos->count()]) }}
                        </span>
                    </div>
                    @endif
                    @endif

                    {{-- Trust badge stack — independently-earned signals instead of one binary "Verified" --}}
                    @php $badges = $profile->trustBadges(); @endphp
                    <div class="absolute top-2 left-1 flex flex-col gap-1 items-start">
                        @if ($badges['cnic'])
                        <span class="flex items-center gap-1 bg-green-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow">🪪 {{ __('db.CNIC Verified') }}</span>
                        @endif
                        @if ($badges['payment'])
                        <span class="flex items-center gap-1 bg-blue-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow">💳 {{ __('db.Payment Verified') }}</span>
                        @endif
                        @if ($badges['guardian'])
                        <span class="flex items-center gap-1 bg-purple-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow">👨‍👩‍👦 {{ __('db.Guardian Verified') }}</span>
                        @endif
                    </div>

                    {{-- Match % badge --}}
                    @if ($matchPercentage > 0)
                    <div class="absolute top-2 right-1">
                        <span class="flex items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-full shadow
                                {{ $matchPercentage >= 80 ? 'bg-green-500 text-white' :
                                   ($matchPercentage >= 50 ? 'bg-yellow-400 text-white' : 'bg-white text-gray-600') }}">
                            {{ __('db.:percent% Match', ['percent' => $matchPercentage]) }}
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Basic Info --}}
                <div class="p-6">
                    <div class="flex justify-between items-start flex-wrap gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">
                                {{ __('db.:age years, :city', ['age' => $profile->age, 'city' => $profile->city]) }}
                                @if ($profile->country !== 'Pakistan') · {{ $profile->country }} @endif
                            </h3>
                            <p class="text-gray-500 mt-1">
                                {{ ucfirst(str_replace('_', ' ', $profile->marital_status)) }}
                                @if (in_array($profile->marital_status, ['divorced', 'widowed', 'separated', 'married']) && !is_null($profile->has_children))
                                · {{ $profile->has_children ? __('db.Has children') . ($profile->children_count ? " ({$profile->children_count})" : '') : __('db.No children') }}
                                @endif
                                @if ($profile->sect) · {{ $profile->sect }} @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ __('db.Member since :date', ['date' => $profile->created_at->format('M Y')]) }}
                                @if ($profile->last_active_at && $profile->last_active_at->gt(now()->subDays(7)))
                                · <span class="text-green-600">{{ __('db.Active :time', ['time' => $profile->last_active_at->diffForHumans()]) }}</span>
                                @endif
                            </p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 flex-wrap">
                            @if ($sentInterest && $sentInterest->status === 'accepted')
                            <a href="{{ route('nikah.messages.show', $sentInterest) }}"
                                class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">
                                💬 {{ __('db.Messages') }}
                            </a>
                            @elseif ($sentInterest && $sentInterest->status === 'pending')
                            <button disabled class="bg-gray-200 text-gray-500 text-sm px-4 py-2 rounded-lg">
                                ✓ {{ __('db.Interest Sent') }}
                            </button>
                            @elseif (!$sentInterest)
                            <form method="POST" action="{{ route('nikah.interest.send', $profile) }}">
                                @csrf
                                <button class="bg-pink-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-pink-700">
                                    💌 {{ __('db.Express Interest') }}
                                </button>
                            </form>
                            @endif

                            <form method="POST" action="{{ route('nikah.save', $profile) }}">
                                @csrf
                                <button title="{{ $isSaved ? __('db.Remove from saved') : __('db.Save profile') }}"
                                    class="px-4 py-2 rounded-lg border text-sm transition
                                    {{ $isSaved ? 'bg-yellow-50 border-yellow-300 text-yellow-600' : 'border-gray-200 text-gray-400 hover:border-yellow-300 hover:text-yellow-500' }}">
                                    {{ $isSaved ? '★ ' . __('db.Saved') : '☆ ' . __('db.Save') }}
                                </button>
                            </form>

                            {{-- Report --}}
                            <div x-data="{ show: false }" class="relative">
                                <button @click="show = !show" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-400 hover:text-red-500 text-sm">
                                    ⋯
                                </button>
                                <div x-show="show" @click.away="show = false" x-cloak
                                    class="absolute right-0 mt-1 bg-white border rounded-lg shadow-lg z-10 w-36 text-sm overflow-hidden">
                                    <form method="POST" action="{{ route('nikah.block', $profile) }}">
                                        @csrf
                                        <button class="w-full text-left px-3 py-2 hover:bg-gray-50 text-gray-600">🚫 {{ __('db.Block') }}</button>
                                    </form>
                                    <button @click="show = false; document.getElementById('report-form').classList.toggle('hidden')"
                                        class="w-full text-left px-3 py-2 hover:bg-gray-50 text-red-500">⚑ {{ __('db.Report') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Report Form --}}
                    <div id="report-form" class="hidden mt-3">
                        <form method="POST" action="{{ route('nikah.report', $profile) }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="reason" placeholder="{{ __('db.Reason for reporting') }}"
                                class="flex-1 border-gray-300 rounded text-sm px-3 py-2" required>
                            <button class="bg-red-600 text-white px-3 py-2 rounded text-sm">{{ __('db.Submit Report') }}</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Profile Details --}}
                <div class="lg:col-span-2 space-y-6">

                    @if ($profile->about)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">{{ __('db.About') }}</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $profile->about }}</p>
                    </div>
                    @endif

                    @if ($profile->expectations)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">{{ __('db.Looking For') }}</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $profile->expectations }}</p>
                    </div>
                    @endif

                    {{-- Contact Info (only after acceptance) --}}
                    @if ($hasAcceptedInterest)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                        <h4 class="font-semibold text-green-800 mb-3">✅ {{ __('db.Contact Information') }}</h4>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Guardian Name') }}</dt>
                                <dd class="font-medium">{{ $profile->guardian_name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Guardian Relation') }}</dt>
                                <dd>{{ $profile->guardian_relation ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Guardian Contact') }}</dt>
                                <dd class="font-medium text-green-700">{{ $profile->guardian_contact }}</dd>
                            </div>
                        </dl>
                        @if ($interest)
                        <div class="mt-3">
                            <a href="{{ route('nikah.messages.show', $interest) }}"
                                class="inline-block bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">
                                💬 {{ __('db.Open Messages') }}
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif

                </div>

                {{-- Right: Details Sidebar --}}
                <div class="space-y-4">
                    {{-- Trust Badge Stack --}}
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-green-600 text-lg">✅</span>
                            <h4 class="font-semibold text-green-800 text-sm">{{ __('db.Trust & Safety') }}</h4>
                        </div>
                        <ul class="text-xs space-y-1">
                            <li class="{{ $badges['cnic'] ? 'text-green-700' : 'text-gray-400' }}">{{ $badges['cnic'] ? '✓' : '○' }} {{ __('db.CNIC verified by admin') }}</li>
                            <li class="{{ $badges['payment'] ? 'text-green-700' : 'text-gray-400' }}">{{ $badges['payment'] ? '✓' : '○' }} {{ __('db.Verification fee payment confirmed') }}</li>
                            <li class="{{ $badges['guardian'] ? 'text-green-700' : 'text-gray-400' }}">{{ $badges['guardian'] ? '✓' : '○' }} {{ __('db.Guardian contact confirmed by admin') }}</li>
                        </ul>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">{{ __('db.Details') }}</h4>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Age') }}</dt>
                                <dd>{{ $profile->age }}</dd>
                            </div>
                            @if ($profile->height)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Height') }}</dt>
                                <dd>{{ $profile->height }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Status') }}</dt>
                                <dd>{{ ucfirst(str_replace('_', ' ', $profile->marital_status)) }}</dd>
                            </div>
                            @if (in_array($profile->marital_status, ['divorced', 'widowed', 'separated', 'married']) && !is_null($profile->has_children))
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Children') }}</dt>
                                <dd>{{ $profile->has_children ? __('db.Yes') . ($profile->children_count ? " ({$profile->children_count})" : '') : __('db.No') }}</dd>
                            </div>
                            @endif
                            @if (in_array($profile->marital_status, ['divorced', 'widowed', 'separated', 'married']) && $profile->living_situation)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Lives With') }}</dt>
                                <dd>{{ ['alone' => __('db.Alone'), 'with_mother' => __('db.Mother'), 'with_father' => __('db.Father'), 'with_maternal_grandparents' => __('db.Maternal Grandparents'), 'with_paternal_grandparents' => __('db.Paternal Grandparents'), 'with_children' => __('db.Their Children'), 'other' => __('db.Other')][$profile->living_situation] ?? ucfirst(str_replace('_', ' ', $profile->living_situation)) }}</dd>
                            </div>
                            @endif
                            @if ($profile->sect)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Sect') }}</dt>
                                <dd>{{ $profile->sect }}</dd>
                            </div>
                            @endif
                            @if ($profile->education)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Education') }}</dt>
                                <dd>{{ $profile->education }}</dd>
                            </div>
                            @endif
                            @if ($profile->profession)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Profession') }}</dt>
                                <dd>{{ $profile->profession }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.City') }}</dt>
                                <dd>{{ $profile->city }}</dd>
                            </div>
                            @if ($profile->state)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.State / Province') }}</dt>
                                <dd>{{ $profile->state }}</dd>
                            </div>
                            @endif
                            @if ($profile->family_type)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Family Type') }}</dt>
                                <dd>{{ $profile->family_type }}</dd>
                            </div>
                            @endif
                            @if ($profile->ethnicity)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Ethnicity') }}</dt>
                                <dd>{{ $profile->ethnicity }}</dd>
                            </div>
                            @endif
                            @if ($profile->language)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Language') }}</dt>
                                <dd>{{ $profile->language }}</dd>
                            </div>
                            @endif
                            @if ($profile->open_to_polygamy)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Polygamy') }}</dt>
                                <dd>{{ __('db.Open to it') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    @if ($profile->prayer_frequency || $profile->hijab_or_beard || $profile->smokes || $profile->diet)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">{{ __('db.Deen & Lifestyle') }}</h4>
                        <dl class="text-sm space-y-2">
                            @if ($profile->prayer_frequency)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Prayer Regularity') }}</dt>
                                <dd>{{ ucfirst($profile->prayer_frequency) }}</dd>
                            </div>
                            @endif
                            @if ($profile->hijab_or_beard)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Hijab/Beard') }}</dt>
                                <dd>{{ ucfirst($profile->hijab_or_beard) }}</dd>
                            </div>
                            @endif
                            @if ($profile->smokes)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Smoking') }}</dt>
                                <dd>{{ ucfirst($profile->smokes) }}</dd>
                            </div>
                            @endif
                            @if ($profile->diet)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Diet') }}</dt>
                                <dd>{{ ucfirst(str_replace('_', ' ', $profile->diet)) }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                    @endif

                    {{-- Match Score --}}
                    @if ($matchPercentage > 0)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3">{{ __('db.Match Score') }}</h4>
                        <div class="text-center">
                            <div class="text-4xl font-bold {{ $matchPercentage >= 80 ? 'text-green-600' : ($matchPercentage >= 50 ? 'text-yellow-600' : 'text-gray-500') }}">
                                {{ $matchPercentage }}%
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ __('db.Based on your preferences') }}</p>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                            <div class="h-2 rounded-full {{ $matchPercentage >= 80 ? 'bg-green-500' : ($matchPercentage >= 50 ? 'bg-yellow-400' : 'bg-gray-300') }}"
                                style="width: {{ $matchPercentage }}%"></div>
                        </div>
                        @if (!empty($matchCriteria))
                        <ul class="mt-3 space-y-1">
                            @foreach ($matchCriteria as $c)
                            <li class="text-xs {{ $c['matched'] ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $c['matched'] ? '✓' : '✗' }} {{ $c['label'] }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        <a href="{{ route('nikah.edit') }}" class="block text-center text-xs text-gray-400 hover:underline mt-2">
                            {{ __('db.Improve by updating your preferences') }} →
                        </a>
                    </div>
                    @endif



                </div>
            </div>

        </div>
    </div>
</x-app-layout>