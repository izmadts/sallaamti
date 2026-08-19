<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ $user->name }}</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Users</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">


            @if ($errors->any())
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Profile Details (editable) --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ $user->avatarUrl() }}" class="w-16 h-16 rounded-full object-cover">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">{{ $user->name }}</h3>
                        <p class="text-xs text-gray-400">Joined {{ $user->created_at->format('d F Y') }} {{ $user->provider ? '· ' . $user->joinSourceLabel() : '' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.users.update-details', $user) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" value="Name" />
                            <x-text-input id="name" name="name" class="w-full mt-1" :value="old('name', $user->name)" required />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="w-full mt-1" :value="old('email', $user->email)" />
                        </div>
                        <div>
                            <x-input-label for="phone" value="Phone" />
                            <x-text-input id="phone" name="phone" class="w-full mt-1" :value="old('phone', $user->phone)" />
                        </div>
                        <div>
                            <x-input-label for="city" value="City" />
                            <x-text-input id="city" name="city" class="w-full mt-1" :value="old('city', $user->city)" />
                        </div>
                        <div>
                            <x-input-label for="gender" value="Gender" />
                            <select id="gender" name="gender" class="w-full mt-1 border-gray-300 rounded-lg text-sm">
                                <option value="">—</option>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="avatar" value="Avatar" />
                            <input id="avatar" type="file" name="avatar" accept="image/*" class="w-full mt-1 text-sm">
                        </div>
                    </div>
                    @if ($user->hasRole('counselor'))
                    <div>
                        <x-input-label for="counselor_bio" value="Counselor specialty / bio (shown to members picking a counselor)" />
                        <textarea id="counselor_bio" name="counselor_bio" rows="2" maxlength="500" class="w-full mt-1 border-gray-300 rounded-lg text-sm">{{ old('counselor_bio', $user->counselor_bio) }}</textarea>
                    </div>
                    @endif
                    <p class="text-xs text-amber-600">Changing the email clears this account's verification status — they'll need to verify the new address.</p>
                    <x-primary-button>Save Details</x-primary-button>
                </form>

                @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.send-password-reset', $user) }}" class="mt-3">
                    @csrf
                    <x-secondary-button type="submit" :disabled="!$user->email">Send Password Reset Link</x-secondary-button>
                    @unless ($user->email)
                    <span class="text-xs text-gray-400 ml-2">No email on file</span>
                    @endunless
                </form>
                @endif
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