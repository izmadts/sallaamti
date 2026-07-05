<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Nikah Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
            <div class="mb-4 p-4 bg-green-50 text-green-700 rounded">
                {{ session('status') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        @if ($profile->photo)
                        <div class="mb-6">
                            <img src="{{ route('nikah.file', [$profile, 'photo']) }}" class="w-32 h-32 object-cover rounded-lg">
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
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Country</dt>
                                <dd>{{ $profile->country }}</dd>
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
                            <img src="{{ route('nikah.photos.show', $photo) }}" class="w-24 h-24 object-cover rounded-lg border-2 {{ $photo->is_primary ? 'border-pink-500' : 'border-gray-200' }}">

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
                    <form method="POST" action="{{ route('nikah.toggle-active') }}" class="inline">
                        @csrf
                        <button class="px-4 py-2 text-sm rounded {{ $profile->is_active ? 'bg-gray-200 text-gray-700' : 'bg-green-600 text-white' }}">
                            {{ $profile->is_active ? 'Hide My Profile' : 'Activate My Profile' }}
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>