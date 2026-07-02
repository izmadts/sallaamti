<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nikah Profiles — Admin Review</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            <!-- Filters -->
            <form method="GET" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs text-gray-500">Search (name, email, city, CNIC)</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="border-gray-300 rounded text-sm block w-56">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Status</label>
                    <select name="status" class="border-gray-300 rounded text-sm block">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Gender</label>
                    <select name="gender" class="border-gray-300 rounded text-sm block">
                        <option value="">All</option>
                        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded">Filter</button>
                <a href="{{ route('admin.nikah.verifications') }}" class="text-sm text-gray-500 px-2">Reset</a>
            </form>

            <!-- Profiles -->
            @forelse ($profiles as $profile)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-start flex-wrap gap-3">
                    <div class="flex gap-4">
                        @if ($profile->photo)
                        <img src="{{ route('nikah.file', [$profile, 'photo']) }}" class="w-20 h-20 object-cover rounded-lg border">
                        @else
                        <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs">No Photo</div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-lg">{{ $profile->user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $profile->user->email }}</p>
                            <p class="text-sm text-gray-500">{{ $profile->age }} yrs, {{ ucfirst($profile->user->gender) }}, {{ $profile->city }}</p>
                            <p class="text-sm text-gray-500">CNIC: {{ $profile->cnic_number }}</p>
                            <p class="text-sm text-gray-500">Guardian: {{ $profile->guardian_name }} ({{ $profile->guardian_contact }})</p>
                        </div>
                        <!-- CNIC Images -->
                        <div class="mt-4 flex gap-4 flex-wrap">
                            @if ($profile->cnic_front_image)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">CNIC Front:</p>
                                <a href="{{ route('nikah.file', [$profile, 'cnic_front_image']) }}" target="_blank">
                                    <img class="w-20 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs" src="{{ route('nikah.file', [$profile, 'cnic_front_image']) }}" class="w-40 rounded border hover:opacity-80">
                                </a>
                            </div>
                            @endif
                            @if ($profile->cnic_back_image)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">CNIC Back:</p>
                                <a href="{{ route('nikah.file', [$profile, 'cnic_back_image']) }}" target="_blank">
                                    <img class="w-20 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs" src="{{ route('nikah.file', [$profile, 'cnic_back_image']) }}" class="w-40 rounded border hover:opacity-80">
                                </a>
                            </div>
                            @endif
                            @if (!$profile->cnic_front_image && !$profile->cnic_back_image)
                            <p class="text-sm text-gray-400">No CNIC images uploaded.</p>
                            @endif
                        </div>

                    </div>

                    <span class="text-xs px-2 py-1 rounded-full
                            {{ $profile->verification_status === 'verified' ? 'bg-green-100 text-green-800' :
                               ($profile->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($profile->verification_status) }}
                    </span>
                </div>

                @if ($profile->rejection_reason)
                <p class="text-sm text-red-600 mt-2">Rejection reason: {{ $profile->rejection_reason }}</p>
                @endif

                <!-- Payment Status Button -->
                <div class="mt-3">
                    @if ($profile->payment_status === 'submitted')
                    <a href="{{ route('admin.nikah.payments', $profile) }}" class="inline-block bg-yellow-500 text-white text-sm px-4 py-2 rounded hover:bg-yellow-600">
                        💳 Verify Payment
                    </a>
                    @elseif ($profile->payment_status === 'confirmed')
                    <span class="inline-block bg-green-100 text-green-800 text-sm px-4 py-2 rounded">
                        ✅ Payment Confirmed
                    </span>
                    @elseif ($profile->payment_status === 'rejected')
                    <span class="inline-block bg-red-100 text-red-800 text-sm px-4 py-2 rounded">
                        ❌ Payment Rejected
                    </span>
                    @else
                    <button disabled class="inline-block bg-gray-200 text-gray-500 text-sm px-4 py-2 rounded cursor-not-allowed">
                        ⏳ Payment Pending (Not Submitted)
                    </button>
                    @endif
                </div>

                <!-- Actions -->
                <div class="mt-4 flex gap-3 flex-wrap">
                    @if ($profile->verification_status !== 'verified')
                    <form method="POST" action="{{ route('admin.nikah.approve', $profile) }}">
                        @csrf
                        <button class="bg-green-600 text-white text-sm px-4 py-2 rounded hover:bg-green-700">Approve</button>
                    </form>
                    @endif

                    @if ($profile->verification_status !== 'rejected')
                    <form method="POST" action="{{ route('admin.nikah.reject', $profile) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="rejection_reason" placeholder="Reason for rejection" class="border-gray-300 rounded text-sm px-2" required>
                        <button class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700">Reject</button>
                    </form>
                    @endif
                </div>

            </div>
            @empty
            <p class="text-gray-500">No profiles match your filters.</p>
            @endforelse

            {{ $profiles->links() }}
        </div>
    </div>
</x-admin-layout>