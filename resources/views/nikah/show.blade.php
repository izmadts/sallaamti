<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Nikah Profile') }}
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
                    <p class="text-sm font-semibold text-red-800">⛔ Your profile has been suspended by our moderation team</p>
                    <p class="text-sm text-red-700 mt-1">Reason: {{ $profile->suspension_reason }}</p>
                    <p class="text-xs text-red-600 mt-2">Your profile is hidden from search and cannot be reactivated from this page. Please contact support if you believe this is a mistake.</p>
                </div>
                @endif
                @php $completeness = $profile->completenessPercentage(); @endphp
                @if ($completeness < 100)
                <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-1">
                        <p class="text-sm font-medium text-gray-700">Profile Completeness</p>
                        <p class="text-sm font-semibold text-teal-700">{{ $completeness }}%</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-teal-600 h-2 rounded-full" style="width: {{ $completeness }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">A more complete profile gets more interest. <a href="{{ route('nikah.edit') }}" class="text-teal-700 hover:underline">Add missing details →</a></p>
                </div>
                @endif
                <div class="flex">
                    <div class="mb-6 flex-1">
                        @if ($profile->payment_status === 'unpaid')
                        <span class="inline-block bg-gray-100 text-gray-700 text-sm font-medium px-3 py-1 rounded-full">💳 Payment Required</span>
                        <a href="{{ route('nikah.payment') }}" class="text-sm text-pink-600 ml-2 hover:underline">Pay Now →</a>
                        @elseif ($profile->payment_status === 'submitted')
                        <span class="inline-block bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">⏳ Payment Under Review</span>
                        @elseif ($profile->payment_status === 'confirmed')
                        <span class="inline-block bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">✅ Payment Confirmed</span>
                        @elseif ($profile->payment_status === 'rejected')
                        <span class="inline-block bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">❌ Payment Rejected</span>
                        <a href="{{ route('nikah.payment') }}" class="text-sm text-pink-600 ml-2 hover:underline">Resubmit →</a>
                        @endif
                        <p class="text-gray-500 text-sm mt-2">Your Payment & Profile is under review by our team. It will become visible in search once verified.</p>
                    </div>
                    <!-- Verification Status Badge -->
                    <div class="mb-6 flex-1">
                        @if ($profile->verification_status === 'pending')
                        <span class="inline-block bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">
                            ⏳ Pending Verification
                        </span>
                        <p class="text-gray-500 text-sm mt-2">Your profile is under review by our team. It will become visible in search once verified.</p>
                        @elseif ($profile->verification_status === 'verified')
                        <span class="inline-block bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                            ✅ Verified
                        </span>
                        @elseif ($profile->verification_status === 'rejected')
                        <span class="inline-block bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">
                            ❌ Rejected
                        </span>
                        @if ($profile->rejection_reason)
                        <p class="text-red-600 text-sm mt-2">Reason: {{ $profile->rejection_reason }}</p>
                        @endif
                        @endif
                    </div>
                </div>

                {{-- Trust Badge Stack --}}
                @php $badges = $profile->trustBadges(); @endphp
                <div class="mb-6 flex flex-wrap gap-2">
                    <span class="text-xs px-2 py-1 rounded-full {{ $badges['cnic'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-400' }}">🪪 CNIC {{ $badges['cnic'] ? 'Verified' : 'Not yet verified' }}</span>
                    <span class="text-xs px-2 py-1 rounded-full {{ $badges['payment'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-400' }}">💳 Payment {{ $badges['payment'] ? 'Verified' : 'Not yet verified' }}</span>
                    <span class="text-xs px-2 py-1 rounded-full {{ $badges['guardian'] ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-400' }}">👨‍👩‍👦 Guardian {{ $badges['guardian'] ? 'Verified' : 'Not yet verified' }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        @if ($profile->photo)
                        <div class="mb-6">
                            <img src="{{ route('nikah.file', [$profile, 'photo']) }}" alt="Your profile photo" class="w-32 h-32 object-cover rounded-lg">
                        </div>
                        @endif
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">Basic Information</h3>
                        <dl class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Age</dt>
                                <dd>{{ $profile->age }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Height</dt>
                                <dd>{{ $profile->height ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Marital Status</dt>
                                <dd>{{ ucfirst(str_replace('_', ' ', $profile->marital_status)) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Sect</dt>
                                <dd>{{ $profile->sect ?: '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">Education & Location</h3>
                        <dl class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Education</dt>
                                <dd>{{ $profile->education ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Profession</dt>
                                <dd>{{ $profile->profession ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">City</dt>
                                <dd>{{ $profile->city }}</dd>
                            </div>
                            @if ($profile->state)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">State / Province</dt>
                                <dd>{{ $profile->state }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Country</dt>
                                <dd>{{ $profile->country }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Ethnicity</dt>
                                <dd>{{ $profile->ethnicity ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Language</dt>
                                <dd>{{ $profile->language ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Open to Polygamy</dt>
                                <dd>{{ $profile->open_to_polygamy ? 'Yes' : 'No' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">Deen & Lifestyle</h3>
                        <dl class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Prayer Regularity</dt>
                                <dd>{{ $profile->prayer_frequency ? ucfirst($profile->prayer_frequency) : '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Hijab/Beard</dt>
                                <dd>{{ $profile->hijab_or_beard ? ucfirst($profile->hijab_or_beard) : '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Smoking</dt>
                                <dd>{{ $profile->smokes ? ucfirst($profile->smokes) : '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Diet</dt>
                                <dd>{{ $profile->diet ? ucfirst(str_replace('_', ' ', $profile->diet)) : '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">Family / Guardian</h3>
                        <dl class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Family Type</dt>
                                <dd>{{ $profile->family_type ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Guardian Name</dt>
                                <dd>{{ $profile->guardian_name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Guardian Relation</dt>
                                <dd>{{ $profile->guardian_relation ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Guardian Contact</dt>
                                <dd>{{ $profile->guardian_contact }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">Visibility</h3>
                        <dl class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Status</dt>
                                <dd>{{ ucfirst($profile->visibility) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Active</dt>
                                <dd>{{ $profile->is_active ? 'Yes' : 'No' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">My Photos
                        <span class="text-xs text-gray-400 font-normal">({{ $profile->photos?->count() ?? 0 }}/5 — only visible after mutual interest accepted)</span>
                    </h3>

                    @if ($profile->photos && $profile->photos->count() > 0)
                    <div class="flex flex-wrap gap-3 mb-4">
                        @foreach ($profile->photos as $photo)
                        <div class="relative">
                            <img src="{{ route('nikah.photos.show', $photo) }}" alt="{{ $photo->is_primary ? 'Primary profile photo' : 'Profile photo' }}" class="w-24 h-24 object-cover rounded-lg border-2 {{ $photo->is_primary ? 'border-pink-500' : 'border-gray-200' }}">

                            @if ($photo->is_primary)
                            <span class="absolute top-1 left-1 text-xs bg-pink-500 text-white px-1 rounded">Primary</span>
                            @endif

                            <div class="absolute top-1 right-1 flex flex-col gap-1">
                                @if (!$photo->is_primary)
                                <form method="POST" action="{{ route('nikah.photos.primary', $photo) }}">
                                    @csrf
                                    <button class="text-xs bg-white text-gray-600 px-1 rounded shadow">⭐</button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('nikah.photos.destroy', $photo) }}"
                                    onsubmit="return confirm('Remove this photo?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs bg-white text-red-600 px-1 rounded shadow">✕</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-400 mb-4">No photos uploaded yet.</p>
                    @endif

                    @if (($profile->photos?->count() ?? 0) < 5)
                        <form method="POST" action="{{ route('nikah.photos.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-3">
                            <input type="file" name="photos[]" accept="image/*" multiple
                                class="text-sm border border-gray-300 rounded px-2 py-1">
                            <x-primary-button>Upload</x-primary-button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">You can select multiple photos at once. Max 5 total.</p>
                        </form>
                        @endif
                </div>
                @if ($profile->about)
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">About</h3>
                    <p class="text-sm text-gray-600">{{ $profile->about }}</p>
                </div>
                @endif

                @if ($profile->expectations)
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">Looking For</h3>
                    <p class="text-sm text-gray-600">{{ $profile->expectations }}</p>
                </div>
                @endif

                <div class="mt-8 flex justify-end">
                    <a href="{{ route('nikah.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                        Edit Profile
                    </a>
                    @unless ($profile->isSuspended())
                    <form method="POST" action="{{ route('nikah.toggle-active') }}" class="inline">
                        @csrf
                        <button class="px-4 py-2 text-sm rounded {{ $profile->is_active ? 'bg-gray-200 text-gray-700' : 'bg-green-600 text-white' }}">
                            {{ $profile->is_active ? 'Hide My Profile' : 'Activate My Profile' }}
                        </button>
                    </form>
                    @endunless
                </div>

                @if ($profile->verification_status === 'verified' && $profile->is_active && !$profile->isSuspended())
                <div class="mt-6 bg-teal-50 border border-teal-100 rounded-lg p-5">
                    <h3 class="font-semibold text-gray-700 mb-1">📤 Share My Profile</h3>
                    <p class="text-xs text-gray-500 mb-3">A safe preview link (no photos, no guardian contact) — share it on WhatsApp or anywhere else to invite people to view your profile on Sallaamti. This link works even if your visibility is set to Private below — it's the only way people can view a Private profile.</p>
                    @php $shareUrl = route('nikah.public-view', $profile->public_token); @endphp
                    <div class="flex gap-2 items-center mb-3">
                        <input type="text" readonly value="{{ $shareUrl }}" id="nikah-share-link" class="flex-1 border-gray-300 rounded-md text-xs bg-white">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('nikah-share-link').value); this.innerText='✅ Copied'; setTimeout(() => this.innerText='Copy', 1500)"
                            class="bg-gray-100 text-gray-700 text-xs px-3 py-2 rounded hover:bg-gray-200">Copy</button>
                    </div>
                    <a href="https://wa.me/?text={{ urlencode('View my profile on Sallaamti: ' . $shareUrl) }}" target="_blank"
                        class="inline-flex items-center gap-2 bg-green-500 text-white text-sm px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fab fa-whatsapp"></i> Share on WhatsApp
                    </a>
                </div>
                @endif

            </div>

            </div>
            </div>
        </div>
    </div>
</x-app-layout>