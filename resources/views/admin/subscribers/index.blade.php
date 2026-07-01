<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Newsletter Subscribers
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage newsletter subscribers and monitor subscription status.
                </p>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>
            @endif
            {{-- Statistics --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <p class="text-sm text-gray-500">Total Subscribers</p>
                    <h2 class="mt-2 text-3xl font-bold text-gray-800">
                        {{ $stats['total'] }}
                    </h2>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <p class="text-sm text-green-600">Verified</p>
                    <h2 class="mt-2 text-3xl font-bold text-green-600">
                        {{ $stats['verified'] }}
                    </h2>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <p class="text-sm text-yellow-600">Pending</p>
                    <h2 class="mt-2 text-3xl font-bold text-yellow-500">
                        {{ $stats['pending'] }}
                    </h2>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <p class="text-sm text-red-600">Inactive</p>
                    <h2 class="mt-2 text-3xl font-bold text-red-600">
                        {{ $stats['inactive'] }}
                    </h2>
                </div>
            </div>
            
            <livewire:tables.subscriber-table />

        </div>
    </div>
</x-app-layout>