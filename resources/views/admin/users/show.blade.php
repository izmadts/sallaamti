<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ $user->name }}</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Users</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">


            {{-- Profile Summary --}}
            <div class="bg-white rounded-lg shadow-sm p-6 flex items-center gap-4">
                <img src="{{ $user->avatarUrl() }}" class="w-16 h-16 rounded-full object-cover">
                <div>
                    <h3 class="font-semibold text-gray-800 text-lg">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }} | {{ $user->phone }}</p>
                    <p class="text-sm text-gray-500">{{ $user->city }} | Gender: {{ ucfirst($user->gender ?? '—') }}</p>
                    <p class="text-xs text-gray-400">Joined {{ $user->created_at->format('d F Y') }}</p>
                </div>
            </div>

            {{-- Role Assignment --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Assign Roles</h3>
                <form method="POST" action="{{ route('admin.users.role', $user) }}">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        @foreach ($allRoles as $role)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                {{ $user->id === auth()->id() && $role->name === 'admin' ? 'disabled' : '' }}>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $role->name === 'admin' ? 'bg-red-100 text-red-700' :
                                       ($role->name === 'teacher' ? 'bg-purple-100 text-purple-700' :
                                       ($role->name === 'counselor' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600')) }}">
                                {{ ucfirst($role->name) }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    <x-primary-button>Update Roles</x-primary-button>
                </form>
            </div>

            {{-- Account Status --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Account Status</h3>
                <p class="text-sm text-gray-500 mb-3">
                    Current: <span class="font-medium {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                </p>
                @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                    @csrf @method('PUT')
                    <button class="text-sm px-4 py-2 rounded {{ $user->is_active ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>
                @endif
            </div>

            {{-- Nikah Profile Summary --}}
            @if ($user->nikahProfile)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Nikah Profile</h3>
                <dl class="text-sm space-y-1">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Verification</dt>
                        <dd>{{ ucfirst($user->nikahProfile->verification_status) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Payment</dt>
                        <dd>{{ ucfirst($user->nikahProfile->payment_status) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">City</dt>
                        <dd>{{ $user->nikahProfile->city }}</dd>
                    </div>
                </dl>
                <a href="{{ route('admin.nikah.verifications') }}?search={{ $user->email }}" class="text-sm text-pink-600 hover:underline mt-2 inline-block">View in Nikah Panel →</a>
            </div>
            @endif

            {{-- Danger Zone --}}
            @if ($user->id !== auth()->id())
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <h3 class="font-semibold text-red-700 mb-2">Danger Zone</h3>
                <p class="text-sm text-red-600 mb-3">Permanently delete this user and all associated data. This cannot be undone.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you absolutely sure? This deletes ALL data for {{ $user->name }} permanently.')">
                    @csrf @method('DELETE')
                    <button class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700">Delete User Permanently</button>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-admin-layout>