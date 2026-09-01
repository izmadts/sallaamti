<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-700 font-semibold">Certificates</span>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-4">

        <div class="flex justify-end">
            <a href="{{ route('admin.certificates.create') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">+ Generate Certificate</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($certificates as $cert)
            <div class="p-4 flex justify-between items-center gap-4 {{ $cert->isPending() ? 'bg-amber-50' : '' }}">
                <div>
                    <p class="font-medium text-gray-800">{{ $cert->user?->name }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $cert->type === 'course' ? $cert->course?->title : $cert->title }}
                        <span class="text-xs px-2 py-0.5 rounded-full ml-1
                            {{ match($cert->status) {
                                'pending' => 'bg-amber-100 text-amber-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-500',
                            } }}">{{ ucfirst($cert->status) }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        @if ($cert->isApproved())
                        {{ $cert->certificate_number }} — issued {{ $cert->issued_at->format('d M Y') }}
                        @elseif ($cert->status === 'rejected' && $cert->rejection_reason)
                        Reason: {{ $cert->rejection_reason }}
                        @else
                        Requested {{ $cert->created_at->format('d M Y') }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if ($cert->isPending())
                    <form method="POST" action="{{ route('admin.certificates.approve', $cert) }}">
                        @csrf
                        <button class="text-sm bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700">Approve</button>
                    </form>
                    <button type="button" onclick="document.getElementById('reject-form-{{ $cert->id }}').classList.toggle('hidden')" class="text-sm text-red-600 hover:underline">Reject</button>
                    @else
                    <a href="{{ route('certificate.download', $cert) }}" class="text-sm text-blue-600 hover:underline">Download</a>
                    <form method="POST" action="{{ route('admin.certificates.destroy', $cert) }}" onsubmit="return confirm('Delete this certificate permanently?')">
                        @csrf @method('DELETE')
                        <button class="text-sm text-red-500 hover:underline">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
            @if ($cert->isPending())
            <form id="reject-form-{{ $cert->id }}" method="POST" action="{{ route('admin.certificates.reject', $cert) }}" class="hidden px-4 pb-4">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="rejection_reason" placeholder="Reason for rejecting" required class="flex-1 border-gray-300 rounded text-sm">
                    <button class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700">Confirm Reject</button>
                </div>
            </form>
            @endif
            @empty
            <p class="p-5 text-gray-400">No certificates issued yet.</p>
            @endforelse
        </div>

        {{ $certificates->links() }}
    </div>
</x-admin-layout>
