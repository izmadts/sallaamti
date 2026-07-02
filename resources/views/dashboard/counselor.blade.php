<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Counselor Panel — {{ $user->name }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-gray-500">Family Counseling module coming soon. You'll manage support queries here.</p>
            </div>
        </div>
    </div>
</x-app-layout>