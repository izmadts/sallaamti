<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Nikah Profiles</h2>
            <a href="{{ route('admin.nikah.profiles.create') }}" class="text-sm font-semibold px-4 py-2 rounded-lg text-white hover:opacity-90" style="background: #0d6b6b">+ Create Profile</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500">Total Profiles</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-green-600">{{ $stats['active'] }}</p>
                    <p class="text-xs text-gray-500">Active</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <p class="text-xl font-bold text-red-600">{{ $stats['suspended'] }}</p>
                    <p class="text-xs text-gray-500">Suspended</p>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs text-gray-500">Search (name, email, phone, city)</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="border-gray-300 rounded text-sm block w-56">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Gender</label>
                    <select name="gender" class="border-gray-300 rounded text-sm block">
                        <option value="">All</option>
                        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Verification</label>
                    <select name="verification_status" class="border-gray-300 rounded text-sm block">
                        <option value="">All</option>
                        <option value="pending" {{ request('verification_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('verification_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Payment</label>
                    <select name="payment_status" class="border-gray-300 rounded text-sm block">
                        <option value="">All</option>
                        <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="submitted" {{ request('payment_status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="confirmed" {{ request('payment_status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="rejected" {{ request('payment_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Status</label>
                    <select name="active" class="border-gray-300 rounded text-sm block">
                        <option value="">All</option>
                        <option value="active" {{ request('active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded">Filter</button>
                <a href="{{ route('admin.nikah.profiles') }}" class="text-sm text-gray-500 px-2">Reset</a>
            </form>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Member</th>
                            <th class="px-4 py-3 text-left">Age / Gender</th>
                            <th class="px-4 py-3 text-left">City</th>
                            <th class="px-4 py-3 text-left">Verification</th>
                            <th class="px-4 py-3 text-left">Payment</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Joined</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($profiles as $profile)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $profile->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $profile->user->email ?: $profile->user->phone }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $profile->age }} / {{ ucfirst($profile->user->gender ?? '—') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $profile->city ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                        {{ $profile->verification_status === 'verified' ? 'bg-green-100 text-green-800' :
                                           ($profile->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($profile->verification_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                        {{ $profile->payment_status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                           ($profile->payment_status === 'submitted' ? 'bg-yellow-100 text-yellow-800' :
                                           ($profile->payment_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600')) }}">
                                    {{ ucfirst($profile->payment_status ?? 'unpaid') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($profile->isSuspended())
                                <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-800">Suspended</span>
                                @elseif ($profile->is_active)
                                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $profile->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.nikah.show', $profile) }}" class="text-xs text-blue-600 hover:underline whitespace-nowrap">🔍 View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">No profiles match your filters.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $profiles->links() }}
        </div>
    </div>
</x-admin-layout>
