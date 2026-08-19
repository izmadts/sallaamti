<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Bulk Messages</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Channel</th>
                            <th class="px-4 py-3 text-left">Subject / Template</th>
                            <th class="px-4 py-3 text-left">Sent By</th>
                            <th class="px-4 py-3 text-left">Recipients</th>
                            <th class="px-4 py-3 text-left">Delivered</th>
                            <th class="px-4 py-3 text-left">Failed</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Sent</th>
                            <th class="px-4 py-3 text-left"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($campaigns as $campaign)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $campaign->channel === 'email' ? '📧 Email' : '💬 WhatsApp' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $campaign->subject ?? $campaign->whatsapp_template_name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $campaign->createdBy?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $campaign->recipient_count }} <span class="text-xs text-gray-400">({{ $campaign->recipient_type === 'subscriber' ? 'subscribers' : 'users' }})</span></td>
                            <td class="px-4 py-3 text-green-600">{{ $campaign->sent_count }}</td>
                            <td class="px-4 py-3 {{ $campaign->failed_count > 0 ? 'text-red-500' : 'text-gray-300' }}">{{ $campaign->failed_count }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $campaign->status === 'completed' ? 'bg-green-100 text-green-700' : ($campaign->status === 'sending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $campaign->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.bulk-messages.show', $campaign) }}" class="text-xs text-teal-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-400">No bulk messages sent yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $campaigns->links() }}

        </div>
    </div>
</x-admin-layout>
