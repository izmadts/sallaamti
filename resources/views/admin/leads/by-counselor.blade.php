<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.leads.index') }}" class="text-gray-400 hover:text-gray-600">Leads</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">By Counselor</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Counselor</th>
                            <th class="px-4 py-3 text-left">City</th>
                            <th class="px-4 py-3 text-right">Assigned</th>
                            <th class="px-4 py-3 text-right">Contacted</th>
                            <th class="px-4 py-3 text-right">Registered</th>
                            <th class="px-4 py-3 text-right">Paid Verified</th>
                            <th class="px-4 py-3 text-left">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($rows as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $row['user']->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $row['city'] ?: '—' }}</td>
                            <td class="px-4 py-3 text-right">{{ $row['assigned'] }}</td>
                            <td class="px-4 py-3 text-right">{{ $row['contacted'] }}</td>
                            <td class="px-4 py-3 text-right">{{ $row['registered'] }}</td>
                            <td class="px-4 py-3 text-right">{{ $row['paid'] }}</td>
                            <td class="px-4 py-3 text-xs text-gray-400">
                                {{ $row['last_activity'] ? \Illuminate\Support\Carbon::parse($row['last_activity'])->format('d M Y, h:i A') : '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No leads assigned to anyone yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
