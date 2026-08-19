<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">User Management</h2>
            <span class="text-sm text-gray-500">{{ $users->total() }} total users</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">


            {{-- Filters --}}
            <form method="GET" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs text-gray-500">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email or phone" class="border-gray-300 rounded text-sm block w-48">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Email contains</label>
                    <input type="text" name="email" value="{{ request('email') }}" placeholder="e.g. gmail.com" class="border-gray-300 rounded text-sm block w-40">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Number contains</label>
                    <input type="text" name="phone" value="{{ request('phone') }}" placeholder="e.g. 0334" class="border-gray-300 rounded text-sm block w-32">
                </div>
                <div>
                    <label class="text-xs text-gray-500" title="Filters by whether the contact field is filled in at all — use this before messaging, since it guarantees every result is actually reachable on that channel.">Can be contacted via</label>
                    <select name="contact" class="border-gray-300 rounded text-sm block">
                        <option value="">Any</option>
                        <option value="has_email" {{ request('contact') === 'has_email' ? 'selected' : '' }}>📧 Has an email</option>
                        <option value="has_phone" {{ request('contact') === 'has_phone' ? 'selected' : '' }}>💬 Has a phone number</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Role</label>
                    <select name="role" class="border-gray-300 rounded text-sm block">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Status</label>
                    <select name="status" class="border-gray-300 rounded text-sm block">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Joined Via</label>
                    <select name="provider" class="border-gray-300 rounded text-sm block">
                        <option value="">All</option>
                        <option value="password" {{ request('provider') === 'password' ? 'selected' : '' }}>📧 Email/Password</option>
                        <option value="whatsapp" {{ request('provider') === 'whatsapp' ? 'selected' : '' }}>💬 WhatsApp OTP</option>
                        <option value="google" {{ request('provider') === 'google' ? 'selected' : '' }}>🔵 Google</option>
                        <option value="facebook" {{ request('provider') === 'facebook' ? 'selected' : '' }}>🔷 Facebook</option>
                        <option value="unknown" {{ request('provider') === 'unknown' ? 'selected' : '' }}>❓ Unknown</option>
                    </select>
                </div>
                <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 px-2">Reset</a>
                <a href="{{ route('admin.users.broadcast', request()->query()) }}" class="ml-auto bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    📨 Message These Users
                </a>
            </form>

            {{-- User Table --}}
            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-4 py-3 text-left">Contact</th>
                            <th class="px-4 py-3 text-left">Roles</th>
                            <th class="px-4 py-3 text-left whitespace-nowrap">Joined Via</th>
                            <th class="px-4 py-3 text-left whitespace-nowrap">Engaged In</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Joined</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $user->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-400">#{{ $user->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p>{{ $user->email }}</p>
                                <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($user->roles as $role)
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                                {{ $role->name === 'admin' ? 'bg-red-100 text-red-700' :
                                                   ($role->name === 'teacher' ? 'bg-purple-100 text-purple-700' :
                                                   ($role->name === 'counselor' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600')) }}">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                    @empty
                                    <span class="text-xs text-gray-400">No role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                    {{ $user->joinSourceLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @if ($user->nikahProfile)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-pink-100 text-pink-700 whitespace-nowrap">💍 Nikah</span>
                                    @endif
                                    @if ($user->enrollments_count > 0)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-teal-100 text-teal-700 whitespace-nowrap">📖 Quran</span>
                                    @endif
                                    @if ($user->counseling_bookings_count > 0)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 whitespace-nowrap">🤝 Counseling</span>
                                    @endif
                                    @if (!$user->nikahProfile && $user->enrollments_count === 0 && $user->counseling_bookings_count === 0)
                                    <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-xs text-blue-600 hover:underline">Manage</a>

                                    @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                        @csrf @method('PUT')
                                        <button class="text-xs {{ $user->is_active ? 'text-orange-500' : 'text-green-600' }} hover:underline">
                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->name }}? This is permanent.')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-500 hover:underline">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-400">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}

        </div>
    </div>
</x-admin-layout>