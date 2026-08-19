<x-admin-layout>
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
            <div class="flex justify-end">
                <a href="{{ route('admin.subscribers.broadcast') }}" class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    📨 Message Verified Subscribers
                </a>
            </div>
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
            {{-- Search --}}
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="p-6">
                    <form method="GET" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-3 items-end">
                        <div class="md:col-span-5">
                            <label class="text-xs text-gray-500">Search (email)</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by email..." class="border-gray-300 rounded text-sm block w-56">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Status</label>
                            <select name="status" class="border-gray-300 rounded text-sm block">
                                <option value="">All Status</option>
                                <option value="verified"
                                    @selected(request('status')=='verified' )>
                                    Verified
                                </option>
                                <option value="pending"
                                    @selected(request('status')=='pending' )>
                                    Pending
                                </option>
                                <option value="inactive"
                                    @selected(request('status')=='inactive' )>
                                    Inactive
                                </option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded">
                                Search
                            </button>
                        </div>
                        <div class="md:col-span-2">
                            <a href="{{ route('admin.subscribers.index') }}"
                                class="text-sm text-gray-500 px-2">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
                {{-- Table --}}
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        #
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Subscribed
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($subscribers as $subscriber)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        {{ $subscriber->id }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $subscriber->email }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(!$subscriber->is_active)
                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Inactive
                                        </span>
                                        @elseif($subscriber->verified_at)
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Verified
                                        </span>
                                        @else
                                        <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                            Pending
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $subscriber->created_at->format('d M Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form
                                            action="{{ route('admin.subscribers.destroy',$subscriber) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this subscriber?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="rounded-lg bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        No subscribers found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
</x-admin-layout>