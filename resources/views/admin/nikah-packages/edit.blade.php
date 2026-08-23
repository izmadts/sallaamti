<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.nikah-packages.index') }}" class="text-gray-400 hover:text-gray-600">Nikah Packages</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ $package->name }}</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.nikah-packages.update', $package) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('admin.nikah-packages._form')

                    <div class="flex justify-end pt-2">
                        <x-primary-button>Save Changes</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-admin-layout>
