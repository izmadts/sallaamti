<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.nikah.verifications') }}" class="text-gray-400 hover:text-gray-600">Verifications</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Shared Guardian Contact</span>
        </div>
    </x-slot>

    <div class="max-w-5xl space-y-4">

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-1">{{ $profiles->count() }} profiles list this same guardian contact</h3>
            <p class="text-sm text-gray-500 mb-2"><x-phone-actions :phone="$contact" /></p>

            @if ($allSameDay)
            <p class="text-sm font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2 inline-block">
                🚩 All {{ $profiles->count() }} were created on the same day — a real family almost never adds every
                child's profile in one sitting. Review carefully before approving any of these.
            </p>
            @else
            <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 inline-block">
                ⚠ This is a soft signal, not proof — siblings can legitimately share one guardian's number. Compare
                names, cities and ages below before treating this as suspicious.
            </p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Member</th>
                        <th class="px-4 py-3 text-left">Age / Gender</th>
                        <th class="px-4 py-3 text-left">City</th>
                        <th class="px-4 py-3 text-left">Guardian Name / Relation</th>
                        <th class="px-4 py-3 text-left">Verification</th>
                        <th class="px-4 py-3 text-left">Created</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($profiles as $profile)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs font-mono text-gray-400">#{{ $profile->id }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $profile->user?->name ?? 'Deleted account' }}</p>
                            <p class="text-xs text-gray-400">{{ $profile->user?->email ?: $profile->user?->phone }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $profile->age }} / {{ ucfirst($profile->user?->gender ?? '—') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $profile->city ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $profile->guardian_name ?: '—' }} ({{ $profile->guardian_relation ?: '—' }})</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full
                                    {{ $profile->verification_status === 'verified' ? 'bg-green-100 text-green-800' :
                                       ($profile->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($profile->verification_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $profile->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.nikah.show', $profile) }}" class="text-xs text-blue-600 hover:underline whitespace-nowrap">🔍 View &amp; Verify</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
