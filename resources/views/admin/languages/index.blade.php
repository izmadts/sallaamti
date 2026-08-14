<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Languages</span>
        </div>
    </x-slot>

    <div class="max-w-3xl space-y-6">

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($languages as $language)
            <form method="POST" action="{{ route('admin.languages.update', $language) }}" class="p-4 flex flex-wrap gap-3 items-center">
                @csrf @method('PUT')
                <div class="w-16 text-xs font-mono text-gray-400">{{ $language->language }}</div>
                <input type="text" name="name" value="{{ $language->name }}"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm flex-1 min-w-[120px] focus:outline-none focus:border-teal-500">
                <label class="flex items-center gap-1 text-xs text-gray-600">
                    <input type="checkbox" name="is_rtl" value="1" {{ $language->is_rtl ? 'checked' : '' }}> RTL
                </label>
                <label class="flex items-center gap-1 text-xs text-gray-600">
                    <input type="checkbox" name="is_active" value="1" {{ $language->is_active ? 'checked' : '' }}> Active
                </label>
                @if ($language->is_default)
                <span class="text-xs px-2 py-0.5 rounded-full bg-teal-100 text-teal-700">Default</span>
                @endif
                <div class="flex gap-2 ms-auto">
                    <button class="text-xs font-medium text-teal-700 hover:underline">Save</button>
                    @unless ($language->is_default)
                    <button type="submit" formaction="{{ route('admin.languages.set-default', $language) }}"
                        formmethod="POST" class="text-xs text-teal-600 hover:underline">Make Default</button>
                    @endunless
                </div>
            </form>
            @empty
            <p class="p-5 text-gray-400">No languages yet.</p>
            @endforelse
        </div>

        @foreach ($languages->filter(fn ($l) => !$l->is_default) as $language)
        <form method="POST" action="{{ route('admin.languages.destroy', $language) }}"
            onsubmit="return confirm('Delete {{ $language->name }}? Its translations will remain but be unreachable.')" class="hidden"
            id="delete-language-{{ $language->id }}">
            @csrf @method('DELETE')
        </form>
        @endforeach

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Add Language</h3>
            <form method="POST" action="{{ route('admin.languages.store') }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Locale Code</label>
                    <input type="text" name="language" placeholder="e.g. ar" required maxlength="10"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-28 focus:outline-none focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Display Name</label>
                    <input type="text" name="name" placeholder="e.g. العربية" required maxlength="50"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-48 focus:outline-none focus:border-teal-500">
                </div>
                <label class="flex items-center gap-1 text-xs text-gray-600 pb-2.5">
                    <input type="checkbox" name="is_rtl" value="1"> RTL
                </label>
                <button class="bg-teal-700 text-white text-sm px-4 py-2.5 rounded-lg hover:bg-teal-800">+ Add Language</button>
            </form>
        </div>

    </div>
</x-admin-layout>
