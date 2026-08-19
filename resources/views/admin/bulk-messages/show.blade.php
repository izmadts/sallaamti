<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.bulk-messages.index') }}" class="text-gray-400 hover:text-gray-600">Bulk Messages</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ $bulkMessage->subject ?? $bulkMessage->whatsapp_template_name ?? 'Campaign #' . $bulkMessage->id }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400">Channel</p>
                    <p class="text-gray-700 font-medium">{{ $bulkMessage->channel === 'email' ? '📧 Email' : '💬 WhatsApp' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    <p class="text-gray-700 font-medium">{{ ucfirst($bulkMessage->status) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Delivered</p>
                    <p class="text-green-600 font-medium">{{ $bulkMessage->sent_count }} / {{ $bulkMessage->recipient_count }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Failed</p>
                    <p class="font-medium {{ $bulkMessage->failed_count > 0 ? 'text-red-500' : 'text-gray-400' }}">{{ $bulkMessage->failed_count }}</p>
                </div>
            </div>

            @if ($bulkMessage->body)
            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-xs text-gray-400 mb-1">Message</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $bulkMessage->body }}</p>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Recipient</th>
                            <th class="px-4 py-3 text-left">Address</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Error</th>
                            <th class="px-4 py-3 text-left">Sent At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($recipients as $recipient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $recipient->user->name ?? ($recipient->subscriber ? 'Newsletter subscriber' : '—') }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $recipient->channel_address }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $recipient->status === 'sent' ? 'bg-green-100 text-green-700' : ($recipient->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ ucfirst($recipient->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-red-400">{{ $recipient->error }}</td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $recipient->sent_at?->format('d M Y, H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">No recipients.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $recipients->links() }}

        </div>
    </div>
</x-admin-layout>
