<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nikah Profiles — Admin Review</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Module Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500">Total Profiles</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-green-600">{{ $stats['verified'] }}</p>
                    <p class="text-xs text-gray-500">Verified</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    <p class="text-xs text-gray-500">Pending Review</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
                    <p class="text-xs text-gray-500">Rejected</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <a href="{{ route('admin.nikah.payments') }}" class="block">
                        <p class="text-xl font-bold text-blue-600">{{ $stats['pending_payments'] }}</p>
                        <p class="text-xs text-gray-500">Pending Payments</p>
                    </a>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-gray-800">{{ $stats['male'] }} / {{ $stats['female'] }}</p>
                    <p class="text-xs text-gray-500">Male / Female</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-gray-800">{{ $stats['new_this_week'] }}</p>
                    <p class="text-xs text-gray-500">New This Week</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <a href="{{ route('admin.nikah.reports') }}" class="block">
                        <p class="text-xl font-bold text-orange-600">⚠</p>
                        <p class="text-xs text-gray-500">Reports Queue</p>
                    </a>
                </div>
            </div>

            <!-- Bulk approve (checkboxes below attach via the form="" attribute) -->
            <form id="bulkApproveForm" method="POST" action="{{ route('admin.nikah.verifications.bulk-approve') }}"></form>
            <div class="bg-white p-3 rounded-lg shadow-sm flex items-center justify-between">
                <p class="text-xs text-gray-500">Select profiles with confirmed payment below, then bulk-approve.</p>
                <button type="submit" form="bulkApproveForm" onclick="return confirm('Approve all selected profiles?')"
                    class="bg-green-600 text-white text-sm px-4 py-2 rounded hover:bg-green-700">✅ Approve Selected</button>
            </div>

            <!-- Bulk remind (separate checkbox set, only shown on unpaid/rejected profiles) -->
            <form id="bulkRemindForm" method="POST" action="{{ route('admin.nikah.verifications.bulk-remind') }}"></form>
            <div class="bg-white p-3 rounded-lg shadow-sm flex items-center justify-between">
                <p class="text-xs text-gray-500">Select profiles stuck on the payment step, then nudge them to finish and get verified.</p>
                <button type="submit" form="bulkRemindForm" onclick="return confirm('Send a completion reminder email to all selected profiles?')"
                    class="bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700">📧 Remind Selected</button>
            </div>

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
                        @if ($profile->payment_status === 'confirmed' && $profile->verification_status !== 'verified')
                        <input type="checkbox" name="profile_ids[]" value="{{ $profile->id }}" form="bulkApproveForm" class="mt-1 rounded" title="Select for bulk approval">
                        @endif
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
                            <p class="text-sm text-gray-500">
                                Guardian: {{ $profile->guardian_name }} ({{ $profile->guardian_contact }})
                                @if ($profile->isGuardianVerified())
                                <span class="text-purple-600">✓ Verified</span>
                                @else
                                <form method="POST" action="{{ route('admin.nikah.verify-guardian', $profile) }}" class="inline">
                                    @csrf
                                    <button class="text-xs text-purple-600 hover:underline">Mark Guardian Verified</button>
                                </form>
                                @endif
                            </p>
                            @if (($guardianContactCounts[$profile->guardian_contact] ?? 0) > 1)
                            <p class="text-xs text-orange-600 mt-1">⚠ This guardian contact number appears on {{ $guardianContactCounts[$profile->guardian_contact] }} profiles on this page — review for possible duplicate/fake accounts.</p>
                            @endif
                            @if ($profile->isSuspended())
                            <p class="text-xs text-red-600 mt-1">⛔ Suspended: {{ $profile->suspension_reason }}
                                <form method="POST" action="{{ route('admin.nikah.unsuspend', $profile) }}" class="inline">
                                    @csrf
                                    <button class="underline">Unsuspend</button>
                                </form>
                            </p>
                            @endif
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

                    @if (in_array($profile->payment_status, ['unpaid', 'rejected']))
                    <input type="checkbox" name="profile_ids[]" value="{{ $profile->id }}" form="bulkRemindForm" class="ml-2 align-middle rounded" title="Select for bulk reminder">
                    <form method="POST" action="{{ route('admin.nikah.remind', $profile) }}" class="inline">
                        @csrf
                        <button class="text-sm bg-indigo-100 text-indigo-700 px-3 py-2 rounded hover:bg-indigo-200">📧 Send Reminder</button>
                    </form>
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

                <!-- Moderation Notes -->
                <details class="mt-4">
                    <summary class="text-xs text-gray-500 cursor-pointer">📝 Moderation Notes ({{ $profile->moderationNotes->count() }})</summary>
                    <div class="mt-2 space-y-2">
                        @foreach ($profile->moderationNotes as $note)
                        <div class="bg-gray-50 rounded p-2 text-xs text-gray-600">
                            <span class="font-semibold">{{ $note->admin->name }}</span>
                            <span class="text-gray-400">{{ $note->created_at->format('d M Y, h:i A') }}</span>
                            <p class="mt-0.5">{{ $note->note }}</p>
                        </div>
                        @endforeach
                        <form method="POST" action="{{ route('admin.nikah.notes.store', $profile) }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="note" placeholder="Add an internal note (not visible to the member)" class="flex-1 border-gray-300 rounded text-xs px-2 py-1" required>
                            <button class="bg-gray-600 text-white text-xs px-3 py-1 rounded hover:bg-gray-700">Add</button>
                        </form>
                    </div>
                </details>

            </div>
            @empty
            <p class="text-gray-500">No profiles match your filters.</p>
            @endforelse

            {{ $profiles->links() }}
        </div>
    </div>
</x-admin-layout>