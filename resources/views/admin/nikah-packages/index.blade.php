<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">💍 Nikah Packages</h2>
            <a href="{{ route('admin.nikah-packages.create') }}"
                class="bg-teal-600 hover:bg-teal-700 hover:-translate-y-0.5 transition text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-sm">
                + Add Package
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div class="rounded-xl p-4 bg-white border border-gray-200 text-sm text-gray-600">
                Everything here is editable at any time — price, duration, proposal limits, and the feature list shown on the public pricing page. Deactivate a package (rather than deleting it) to stop it being assignable while keeping the history of clients already on it.
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($packages as $package)
                <div class="p-5 flex justify-between items-start flex-wrap gap-3">
                    <div class="max-w-2xl">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ match($package->color) {
                                    'green' => 'bg-green-50 text-green-700',
                                    'amber' => 'bg-amber-50 text-amber-700',
                                    'blue' => 'bg-blue-50 text-blue-700',
                                    'red' => 'bg-red-50 text-red-700',
                                    'purple' => 'bg-purple-50 text-purple-700',
                                    'teal' => 'bg-teal-50 text-teal-700',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ $package->icon }} {{ $package->name }}
                            </span>
                            @unless ($package->is_active)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Inactive</span>
                            @endunless
                            @if ($package->is_active && !$package->show_on_public_page)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-700">Hidden from public page</span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $package->leads_count }} client{{ $package->leads_count === 1 ? '' : 's' }} on this package</span>
                        </div>
                        <p class="font-medium text-gray-800 mt-2">Rs. {{ number_format($package->price) }} {{ $package->isOneTime() ? '(one-time)' : '/ ' . $package->duration_days . ' days' }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $package->tagline }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $package->proposal_limit ? 'Up to ' . $package->proposal_limit . ' proposals' : 'No proposal cap' }}
                            @if ($package->consultant_level) · {{ $package->consultant_level }} @endif
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs text-gray-400">Order: {{ $package->sort_order }}</span>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.nikah-packages.edit', $package) }}" class="text-sm text-teal-700 hover:underline font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.nikah-packages.destroy', $package) }}" onsubmit="return confirm('Delete this package? Only possible if no clients are on it — deactivate instead if you just want to stop offering it.')">
                                @csrf @method('DELETE')
                                <button class="text-sm text-red-400 hover:text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-500">No packages yet — click "+ Add Package" to create the first one.</p>
                @endforelse
            </div>

            <a href="{{ route('nikah.packages') }}" target="_blank" class="text-sm text-teal-700 hover:underline">👀 View the public pricing page →</a>
        </div>
    </div>
</x-admin-layout>
