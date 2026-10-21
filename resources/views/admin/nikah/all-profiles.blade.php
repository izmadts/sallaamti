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
            {{-- One form, one Filter button, every field below submits
                 together — split into two labeled rows purely for
                 scanability (moderation/queue filters vs. match-finding
                 filters), not because they're handled separately.
                 flex-wrap with each field sized to its own content (not a
                 fixed grid column) so as many fields as actually fit pack
                 into one row before wrapping, rather than an even column
                 count leaving a field stranded alone on its own row. --}}
            <form method="GET" class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Moderation</p>
                <div class="flex flex-wrap gap-3 items-end">
                    <div class="w-56">
                        <label class="text-xs text-gray-500">Search (name, email, phone, city, or #ID)</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="e.g. 123 or #123" class="border-gray-300 rounded text-sm block w-full">
                    </div>
                    <div class="w-28">
                        <label class="text-xs text-gray-500">Gender</label>
                        <select name="gender" class="border-gray-300 rounded text-sm block w-full">
                            <option value="">All</option>
                            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="text-xs text-gray-500">Verification</label>
                        <select name="verification_status" class="border-gray-300 rounded text-sm block w-full">
                            <option value="">All</option>
                            <option value="pending" {{ request('verification_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="rejected" {{ request('verification_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="text-xs text-gray-500">Payment</label>
                        <select name="payment_status" class="border-gray-300 rounded text-sm block w-full">
                            <option value="">All</option>
                            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="submitted" {{ request('payment_status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="confirmed" {{ request('payment_status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="rejected" {{ request('payment_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="w-28">
                        <label class="text-xs text-gray-500">Status</label>
                        <select name="active" class="border-gray-300 rounded text-sm block w-full">
                            <option value="">All</option>
                            <option value="active" {{ request('active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3 mt-5 pt-4 border-t border-gray-100">Match-Finding</p>
                <div class="flex flex-wrap gap-3 items-end">
                    <div class="w-20">
                        <label class="text-xs text-gray-500">Min Age</label>
                        <input type="number" name="min_age" value="{{ request('min_age') }}" class="border-gray-300 rounded text-sm block w-full">
                    </div>
                    <div class="w-20">
                        <label class="text-xs text-gray-500">Max Age</label>
                        <input type="number" name="max_age" value="{{ request('max_age') }}" class="border-gray-300 rounded text-sm block w-full">
                    </div>
                    <div class="w-28">
                        <label class="text-xs text-gray-500">City</label>
                        <input type="text" name="city" value="{{ request('city') }}" placeholder="e.g. Lahore" class="border-gray-300 rounded text-sm block w-full">
                    </div>
                    <div class="w-28">
                        <label class="text-xs text-gray-500">Sect</label>
                        <select name="sect" class="border-gray-300 rounded text-sm block w-full">
                            <option value="">Any</option>
                            @foreach (['Sunni', 'Shia', 'Ahle Hadith', 'Deobandi', 'Other'] as $sect)
                            <option value="{{ $sect }}" {{ request('sect') === $sect ? 'selected' : '' }}>{{ $sect }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-36">
                        <label class="text-xs text-gray-500">Education</label>
                        <select name="education" class="border-gray-300 rounded text-sm block w-full">
                            <option value="">Any</option>
                            @foreach ($educationLevels as $level)
                            <option value="{{ $level }}" {{ request('education') === $level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="text-xs text-gray-500">Marital Status</label>
                        <select name="marital_status" class="border-gray-300 rounded text-sm block w-full">
                            <option value="">Any</option>
                            @foreach (['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'separated' => 'Separated', 'married' => 'Married'] as $val => $label)
                            <option value="{{ $val }}" {{ request('marital_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="text-xs text-gray-500">Country</label>
                        <select name="country" class="border-gray-300 rounded text-sm block w-full">
                            <option value="">Any</option>
                            @foreach ($countries as $country)
                            <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-28">
                        <label class="text-xs text-gray-500">State / Province</label>
                        <input type="text" name="state" value="{{ request('state') }}" placeholder="e.g. Punjab" class="border-gray-300 rounded text-sm block w-full">
                    </div>
                    <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded">Filter</button>
                    <a href="{{ route('admin.nikah.profiles') }}" class="text-sm text-gray-500 px-2">Reset</a>
                </div>
            </form>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Member</th>
                            <th class="px-4 py-3 text-left">Age / Gender</th>
                            <th class="px-4 py-3 text-left">City</th>
                            <th class="px-4 py-3 text-left">Marital Status</th>
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
                            <td class="px-4 py-3 text-xs font-mono text-gray-400">#{{ $profile->id }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $profile->user?->name ?? 'Deleted account' }}</p>
                                <p class="text-xs text-gray-400">
                                    @if ($profile->user?->email)
                                    {{ $profile->user->email }}
                                    @elseif ($profile->user?->phone)
                                    <x-phone-actions :phone="$profile->user->phone" />
                                    @endif
                                </p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $profile->age }} / {{ ucfirst($profile->user?->gender ?? '—') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $profile->city ?: '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $profile->marital_status ? ucfirst(str_replace('_', ' ', $profile->marital_status)) : '—' }}</td>
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
                            <td colspan="10" class="px-4 py-8 text-center text-gray-400">No profiles match your filters.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between gap-3 flex-wrap">
                <form method="GET" class="flex items-center gap-2 text-xs text-gray-500">
                    @foreach (request()->except(['per_page', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="per_page">Rows per page</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()" class="border-gray-300 rounded text-xs">
                        @foreach ([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ $perPage === $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span>of {{ number_format($profiles->total()) }}</span>
                </form>
                {{ $profiles->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
