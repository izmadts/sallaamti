<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Roles Overview</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">✅ {{ session('status') }}</div>
            @endif

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