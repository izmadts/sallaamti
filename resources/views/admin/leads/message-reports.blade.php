<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pending Message Reports</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @forelse ($reports as $report)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold">
                            {{ $report->reporter?->name ?? 'Deleted account' }} reported {{ $report->reported?->name ?? 'deleted account' }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">On the Nikah Counselor conversation for client <strong>{{ $report->lead?->name ?? 'deleted' }}</strong></p>
                        <p class="text-sm text-gray-500 mt-1"><strong>Reason:</strong> {{ $report->reason }}</p>
                        @if ($report->details)
                        <p class="text-sm text-gray-500 mt-1">{{ $report->details }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">{{ $report->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Pending</span>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.leads.message-reports.conversation', $report) }}" class="text-sm text-blue-600 hover:underline">View Conversation</a>
                    @if ($report->lead)
                    <a href="{{ route('admin.leads.show', $report->lead) }}" class="text-sm text-blue-600 hover:underline">View Client</a>
                    @endif
                    <form method="POST" action="{{ route('admin.leads.message-reports.dismiss', $report) }}">
                        @csrf
                        <button class="bg-gray-600 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">Dismiss</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-gray-500">No pending reports.</p>
            @endforelse

            {{ $reports->links() }}
        </div>
    </div>
</x-admin-layout>
