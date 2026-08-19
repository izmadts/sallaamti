<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Roles Overview</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if ($errors->any())
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-4">
                <form method="POST" action="{{ route('admin.users.roles.store') }}" class="flex gap-2">
                    @csrf
                    <x-text-input name="name" class="flex-1" placeholder="e.g. matchmaker" required />
                    <x-primary-button>+ Create Role</x-primary-button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @foreach ($roles as $role)
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-gray-800">{{ ucfirst($role->name) }}</span>
                            <p class="text-sm text-gray-500">{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }} assigned</p>
                        </div>
                        <a href="{{ route('admin.users.index', ['role' => $role->name]) }}" class="text-sm text-blue-600 hover:underline">View Users →</a>
                    </div>

                    @if ($role->name === 'admin')
                    <p class="text-xs text-teal-700 bg-teal-50 border border-teal-100 rounded-lg px-3 py-2 mt-3">
                        ✅ Full access to everything — the admin role always bypasses these permission checks, so there's nothing to configure here.
                    </p>
                    @else
                    <form method="POST" action="{{ route('admin.users.roles.permissions', $role) }}" class="mt-3">
                        @csrf
                        <p class="text-xs text-gray-400 mb-2">
                            What this role can do in the admin panel, beyond its normal member-facing access. Unchecked = no access to that area at all.
                        </p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border border-gray-100 rounded-lg overflow-hidden">
                                <thead>
                                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase">
                                        <th class="text-left px-3 py-2">Area</th>
                                        @foreach ($actions as $actionKey => $actionLabel)
                                        <th class="px-3 py-2">{{ $actionLabel }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($resources as $resourceKey => $resourceLabel)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-700">{{ $resourceLabel }}</td>
                                        @foreach ($actions as $actionKey => $actionLabel)
                                        @php $permName = "{$resourceKey}.{$actionKey}"; @endphp
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox" name="permissions[]" value="{{ $permName }}"
                                                {{ $role->permissions->contains('name', $permName) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-teal-600">
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <x-secondary-button type="submit" class="mt-3">Save Permissions for {{ ucfirst($role->name) }}</x-secondary-button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>