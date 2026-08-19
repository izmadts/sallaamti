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
            <div class="p-4 flex justify-between items-center">
                <div>
                    <p class="font-medium text-gray-800">{{ $cert->user?->name }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $cert->type === 'course' ? $cert->course?->title : $cert->title }}
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 ml-1">{{ $cert->type }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ $cert->certificate_number }} — issued {{ $cert->issued_at->format('d M Y') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('certificate.download', $cert) }}" class="text-sm text-blue-600 hover:underline">Download</a>
                    <form method="POST" action="{{ route('admin.certificates.destroy', $cert) }}" onsubmit="return confirm('Delete this certificate permanently?')">
                        @csrf @method('DELETE')
                        <button class="text-sm text-red-500 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="p-5 text-gray-400">No certificates issued yet.</p>
            @endforelse
        </div>

        {{ $certificates->links() }}
    </div>
</x-admin-layout>
