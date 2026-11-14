<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">📚 Library</h2>
            <a href="{{ route('admin.library.create') }}" class="text-sm font-semibold px-4 py-2 rounded-lg text-white hover:opacity-90" style="background:#0d6b6b">+ Add booklet / document</a>
        </div>
    </x-slot>
    <div class="space-y-4">
        <p class="text-sm text-gray-500">Free PDFs shown on <a href="{{ route('library.index') }}" target="_blank" class="text-teal-700 underline">/library</a>. Only published items are visible. Code (BK1…) matches the ulama review sheet.</p>
        <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr><th class="px-4 py-3 text-left">Code</th><th class="px-4 py-3 text-left">Title</th><th class="px-4 py-3 text-left">Section</th><th class="px-4 py-3 text-left">Pages</th><th class="px-4 py-3 text-left">Views / Downloads / Shares</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($items as $item)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ $item->code ?: '—' }}</td>
                        <td class="px-4 py-3"><span dir="rtl">{{ $item->title }}</span><br><a href="{{ $item->fileUrl() }}" target="_blank" class="text-xs text-teal-700">PDF</a></td>
                        <td class="px-4 py-3 text-gray-500">{{ $categories[$item->category]['en'] ?? $item->category }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->pages ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->view_count }} / {{ $item->download_count }} / {{ $item->share_count }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full font-semibold" style="{{ $item->is_published ? 'background:#dcfce7;color:#166534' : 'background:#f3f4f6;color:#4b5563' }}">{{ $item->is_published ? 'Published' : 'Hidden' }}</span>
                            @if ($item->is_published && $item->isNew())<span class="text-xs px-2 py-1 rounded-full font-semibold" style="background:#fef9c3;color:#854d0e">New</span>@endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <form method="POST" action="{{ route('admin.library.toggle', $item) }}" class="inline">@csrf @method('PATCH')<button class="text-xs text-teal-700 mr-3">{{ $item->is_published ? 'Hide' : 'Publish' }}</button></form>
                            <a href="{{ route('admin.library.edit', $item) }}" class="text-xs text-gray-600 mr-3">Edit</a>
                            {{-- A published item can only be deleted by a full admin (LibraryItemController::destroy()) — hiding the button for anyone else avoids a confusing 403 click. --}}
                            @if (!$item->is_published || auth()->user()->hasRole('admin'))
                            <form method="POST" action="{{ route('admin.library.destroy', $item) }}" class="inline" onsubmit="return confirm('Delete this item and its PDF?')">@csrf @method('DELETE')<button class="text-xs text-red-600">Delete</button></form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No items yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
