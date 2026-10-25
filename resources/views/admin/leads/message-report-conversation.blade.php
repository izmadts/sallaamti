<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reported Conversation</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white shadow-sm rounded-lg p-4 text-sm text-gray-600">
                <p><strong>Client:</strong> {{ $report->lead?->name ?? 'Deleted' }}</p>
                <p><strong>Reporter:</strong> {{ $report->reporter?->name ?? 'Deleted account' }}</p>
                <p><strong>Reported:</strong> {{ $report->reported?->name ?? 'Deleted account' }}</p>
                <p><strong>Reason:</strong> {{ $report->reason }}</p>
                @if ($report->details)
                <p><strong>Details:</strong> {{ $report->details }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded-lg p-4 space-y-3">
                <h3 class="font-semibold text-gray-700 border-b pb-2">Messages ({{ $messages->count() }})</h3>

                @forelse ($messages as $message)
                <div class="p-3 rounded {{ (int) $message->sender_id === (int) $report->reporter_user_id ? 'bg-blue-50' : 'bg-gray-50' }} {{ $report->lead_client_message_id === $message->id ? 'ring-2 ring-red-400' : '' }}">
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span class="font-semibold text-gray-600">{{ $message->sender?->name ?? 'Deleted account' }}</span>
                        <span>{{ $message->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <p class="text-sm text-gray-800">{{ $message->message }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No messages in this conversation yet.</p>
                @endforelse
            </div>

            <a href="{{ route('admin.leads.message-reports.index') }}" class="text-sm text-blue-600 hover:underline">← Back to Reports</a>
        </div>
    </div>
</x-admin-layout>
