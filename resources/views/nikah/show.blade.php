<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.My Nikah Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
            <x-module-nav module="nikah" />
            <div class="flex-1 min-w-0">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($profile->isSuspended())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm font-semibold text-red-800">⛔ {{ __('db.Your profile has been suspended by our moderation team') }}</p>
                    <p class="text-sm text-red-700 mt-1">{{ __('db.Reason:') }} {{ $profile->suspension_reason }}</p>
                    <p class="text-xs text-red-600 mt-2">{{ __('db.Your profile is hidden from search and cannot be reactivated from this page. Please contact support if you believe this is a mistake.') }}</p>
                </div>
                @endif
                @php $completeness = $profile->completenessPercentage(); @endphp
                @if ($completeness < 100)
                <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-1">
                        <p class="text-sm font-medium text-gray-700">{{ __('db.Profile Completeness') }}</p>
                        <p class="text-sm font-semibold text-teal-700">{{ $completeness }}%</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-teal-600 h-2 rounded-full" style="width: {{ $completeness }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ __('db.A more complete profile gets more interest.') }} <a href="{{ route('nikah.edit') }}" class="text-teal-700 hover:underline">{{ __('db.Add missing details') }} →</a></p>
                </div>
                @endif
                <div class="flex">
                    <div class="mb-6 flex-1">
                        @if ($profile->payment_status === 'unpaid')
                        <span class="inline-block bg-gray-100 text-gray-700 text-sm font-medium px-3 py-1 rounded-full">💳 {{ __('db.Payment Required') }}</span>
                        <a href="{{ route('nikah.payment') }}" class="text-sm text-pink-600 ml-2 hover:underline">{{ __('db.Pay Now') }} →</a>
                        @elseif ($profile->payment_status === 'submitted')
                        <span class="inline-block bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">⏳ {{ __('db.Payment Under Review') }}</span>
                        @elseif ($profile->payment_status === 'confirmed')
                        <span class="inline-block bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">✅ {{ __('db.Payment Confirmed') }}</span>
                        @elseif ($profile->payment_status === 'rejected')
                        <span class="inline-block bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">❌ {{ __('db.Payment Rejected') }}</span>
                        <a href="{{ route('nikah.payment') }}" class="text-sm text-pink-600 ml-2 hover:underline">{{ __('db.Resubmit') }} →</a>
                        @endif
                        <p class="text-gray-500 text-sm mt-2">{{ __('db.Your Payment & Profile is under review by our team. It will become visible in search once verified.') }}</p>
                    </div>
                    <!-- Verification Status Badge -->
                    <div class="mb-6 flex-1">
                        @if ($profile->verification_status === 'pending')
                        <span class="inline-block bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">
                            ⏳ {{ __('db.Pending Verification') }}
                        </span>
                        <p class="text-gray-500 text-sm mt-2">{{ __('db.Your profile is under review by our team. It will become visible in search once verified.') }}</p>
                        @elseif ($profile->verification_status === 'verified')
                        <span class="inline-block bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                            ✅ {{ __('db.Verified') }}
                        </span>
                        @elseif ($profile->verification_status === 'rejected')
                        <span class="inline-block bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">
                            ❌ {{ __('db.Rejected') }}
                        </span>
                        @if ($profile->rejection_reason)
                        <p class="text-red-600 text-sm mt-2">{{ __('db.Reason:') }} {{ $profile->rejection_reason }}</p>
                        @endif
                        @endif
                    </div>
                </div>

                {{-- Trust Badge Stack --}}
                @php $badges = $profile->trustBadges(); @endphp
                <div class="mb-6 flex flex-wrap gap-2">
                    <span class="text-xs px-2 py-1 rounded-full {{ $badges['cnic'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-400' }}">🪪 {{ __('db.CNIC :status', ['status' => $badges['cnic'] ? __('db.Verified') : __('db.Not yet verified')]) }}</span>
                    <span class="text-xs px-2 py-1 rounded-full {{ $badges['payment'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-400' }}">💳 {{ __('db.Payment :status', ['status' => $badges['payment'] ? __('db.Verified') : __('db.Not yet verified')]) }}</span>
                    <span class="text-xs px-2 py-1 rounded-full {{ $badges['guardian'] ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-400' }}">👨‍👩‍👦 {{ __('db.Guardian :status', ['status' => $badges['guardian'] ? __('db.Verified') : __('db.Not yet verified')]) }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        @if ($profile->photo)
                        <div class="mb-6">
                            <img src="{{ route('nikah.file', [$profile, 'photo']) }}" alt="{{ __('db.Your profile photo') }}" class="w-32 h-32 object-cover rounded-lg">
                        </div>
                        @endif
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">{{ __('db.Basic Information') }}</h3>
                        <dl class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Age') }}</dt>
                                <dd>{{ $profile->age }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Height') }}</dt>
                                <dd>{{ $profile->height ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Marital Status') }}</dt>
                                <dd>{{ ucfirst(str_replace('_', ' ', $profile->marital_status)) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Sect') }}</dt>
                                <dd>{{ $profile->sect ?: '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">{{ __('db.Education & Location') }}</h3>
                        <dl class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Education') }}</dt>
                                <dd>{{ $profile->education ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Profession') }}</dt>
                                <dd>{{ $profile->profession ?: '—' }}</dd>
                            </div>
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
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Country') }}</dt>
                                <dd>{{ $profile->country }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Ethnicity') }}</dt>
                                <dd>{{ $profile->ethnicity ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Language') }}</dt>
                                <dd>{{ $profile->language ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Open to Polygamy') }}</dt>
                                <dd>{{ $profile->open_to_polygamy ? __('db.Yes') : __('db.No') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">{{ __('db.Deen & Lifestyle') }}</h3>
                        <dl class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Prayer Regularity') }}</dt>
                                <dd>{{ $profile->prayer_frequency ? ucfirst($profile->prayer_frequency) : '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Hijab/Beard') }}</dt>
                                <dd>{{ $profile->hijab_or_beard ? ucfirst($profile->hijab_or_beard) : '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Smoking') }}</dt>
                                <dd>{{ $profile->smokes ? ucfirst($profile->smokes) : '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Diet') }}</dt>
                                <dd>{{ $profile->diet ? ucfirst(str_replace('_', ' ', $profile->diet)) : '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">{{ __('db.Family / Guardian') }}</h3>
                        <dl class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Family Type') }}</dt>
                                <dd>{{ $profile->family_type ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Guardian Name') }}</dt>
                                <dd>{{ $profile->guardian_name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Guardian Relation') }}</dt>
                                <dd>{{ $profile->guardian_relation ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Guardian Contact') }}</dt>
                                <dd>{{ $profile->guardian_contact }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">{{ __('db.Visibility') }}</h3>
                        <dl class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Status') }}</dt>
                                <dd>{{ match($profile->visibility) {
                                    'public' => __('db.Public'),
                                    'members_only' => __('db.Members Only'),
                                    'matchmaker_assisted' => __('db.Matchmaker-Assisted Only'),
                                    'confidential' => __('db.Confidential'),
                                    default => ucfirst($profile->visibility),
                                } }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('db.Active') }}</dt>
                                <dd>{{ $profile->is_active ? __('db.Yes') : __('db.No') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">{{ __('db.My Photos') }}
                        <span class="text-xs text-gray-400 font-normal">{{ __('db.(:count/5 — only visible after mutual interest accepted)', ['count' => $profile->photos?->count() ?? 0]) }}</span>
                    </h3>

                    @if ($profile->photos && $profile->photos->count() > 0)
                    <div class="flex flex-wrap gap-3 mb-4">
                        @foreach ($profile->photos as $photo)
                        <div class="relative">
                            <img src="{{ route('nikah.photos.show', $photo) }}" alt="{{ $photo->is_primary ? __('db.Primary profile photo') : __('db.Profile photo') }}" class="w-24 h-24 object-cover rounded-lg border-2 {{ $photo->is_primary ? 'border-pink-500' : 'border-gray-200' }}">

                            @if ($photo->is_primary)
                            <span class="absolute top-1 left-1 text-xs bg-pink-500 text-white px-1 rounded">{{ __('db.Primary') }}</span>
                            @endif

                            <div class="absolute top-1 right-1 flex flex-col gap-1">
                                @if (!$photo->is_primary)
                                <form method="POST" action="{{ route('nikah.photos.primary', $photo) }}">
                                    @csrf
                                    <button class="text-xs bg-white text-gray-600 px-1 rounded shadow">⭐</button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('nikah.photos.destroy', $photo) }}"
                                    onsubmit="return confirm({!! json_encode(__('db.Remove this photo?')) !!})">
                                    @csrf @method('DELETE')
                                    <button class="text-xs bg-white text-red-600 px-1 rounded shadow">✕</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-400 mb-4">{{ __('db.No photos uploaded yet.') }}</p>
                    @endif

                    @if (($profile->photos?->count() ?? 0) < 5)
                        <form method="POST" action="{{ route('nikah.photos.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-3">
                            <input type="file" name="photos[]" accept="image/*" multiple
                                class="text-sm border border-gray-300 rounded px-2 py-1">
                            <x-primary-button>{{ __('db.Upload') }}</x-primary-button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ __('db.You can select multiple photos at once. Max 5 total.') }}</p>
                        </form>
                        @endif
                </div>
                @if ($profile->about)
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">{{ __('db.About') }}</h3>
                    <p class="text-sm text-gray-600">{{ $profile->about }}</p>
                </div>
                @endif

                @if ($profile->expectations)
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">{{ __('db.Looking For') }}</h3>
                    <p class="text-sm text-gray-600">{{ $profile->expectations }}</p>
                </div>
                @endif

                <div class="mt-8 flex justify-end">
                    <a href="{{ route('nikah.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                        {{ __('db.Edit Profile') }}
                    </a>
                    @unless ($profile->isSuspended())
                    <form method="POST" action="{{ route('nikah.toggle-active') }}" class="inline">
                        @csrf
                        <button class="px-4 py-2 text-sm rounded {{ $profile->is_active ? 'bg-gray-200 text-gray-700' : 'bg-green-600 text-white' }}">
                            {{ $profile->is_active ? __('db.Hide My Profile') : __('db.Activate My Profile') }}
                        </button>
                    </form>
                    @endunless
                </div>

                @if ($profile->verification_status === 'verified' && $profile->is_active && !$profile->isSuspended())
                <div class="mt-6 bg-teal-50 border border-teal-100 rounded-lg p-5">
                    <h3 class="font-semibold text-gray-700 mb-1">📤 {{ __('db.Share My Profile') }}</h3>
                    <p class="text-xs text-gray-500 mb-3">{{ __("db.A safe preview link (no photos, no guardian contact) — share it on WhatsApp or anywhere else to invite people to view your profile on Sallaamti. This link always works, no matter which visibility option you've chosen above — it's the only way people can view a profile that isn't set to \"Anyone can find me.\"") }}</p>
                    @php $shareUrl = route('nikah.public-view', $profile->public_token); @endphp
                    <div class="flex gap-2 items-center mb-3">
                        <input type="text" readonly value="{{ $shareUrl }}" id="nikah-share-link" class="flex-1 border-gray-300 rounded-md text-xs bg-white">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('nikah-share-link').value); this.innerText={!! json_encode('✅ ' . __('db.Copied')) !!}; setTimeout(() => this.innerText={!! json_encode(__('db.Copy')) !!}, 1500)"
                            class="bg-gray-100 text-gray-700 text-xs px-3 py-2 rounded hover:bg-gray-200">{{ __('db.Copy') }}</button>
                    </div>
                    <a href="https://wa.me/?text={{ urlencode(__('db.View my profile on Sallaamti: ') . $shareUrl) }}" target="_blank"
                        class="inline-flex items-center gap-2 bg-green-500 text-white text-sm px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fab fa-whatsapp"></i> {{ __('db.Share on WhatsApp') }}
                    </a>
                </div>
                @endif

            </div>

            </div>
            </div>
        </div>
    </div>
</x-app-layout>