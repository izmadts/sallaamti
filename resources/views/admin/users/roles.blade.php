<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Roles Overview</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white rounded-lg shadow-sm divide-y">
                @foreach ($roles as $role)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <span class="font-medium text-gray-800">{{ ucfirst($role->name) }}</span>
                        <p class="text-sm text-gray-500">{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }} assigned</p>
                    </div>
                    <a href="{{ route('admin.users.index', ['role' => $role->name]) }}" class="text-sm text-blue-600 hover:underline">View Users →</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>