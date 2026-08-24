<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pending Profile Reports</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            @forelse ($reports as $report)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold">
                            {{ $report->reporter?->user?->name ?? 'Deleted profile' }} reported {{ $report->reported?->user?->name ?? 'deleted profile' }}
                            @php $totalReports = $reportCounts[$report->reported_profile_id] ?? 1; @endphp
                            @if ($totalReports > 1)
                            <span class="ml-1 text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full align-middle">⚠ {{ $totalReports }} reports total — repeat offender</span>
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500 mt-1"><strong>Reason:</strong> {{ $report->reason }}</p>
                        @if ($report->details)
                        <p class="text-sm text-gray-500 mt-1">{{ $report->details }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">{{ $report->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Pending</span>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <a href="{{ route('nikah.profile.view', $report->reported_profile_id) }}" target="_blank" class="text-sm text-blue-600 hover:underline">View Reported Profile</a>
                    @if ($report->nikah_interest_id)
                    <a href="{{ route('admin.nikah.reports.conversation', $report) }}" class="text-sm text-blue-600 hover:underline">View Conversation</a>
                    @endif
                    <form method="POST" action="{{ route('admin.nikah.reports.suspend', $report) }}"
                        onsubmit="return confirm('Suspend {{ $report->reported?->user?->name ?? 'this' }}\'s profile? It will be hidden from search immediately.')">
                        @csrf
                        <button class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700">Suspend Profile</button>
                    </form>
                    <form method="POST" action="{{ route('admin.nikah.reports.dismiss', $report) }}">
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
