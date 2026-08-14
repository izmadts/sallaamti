<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Translations</span>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-4">

        <form method="GET" action="{{ route('admin.translations.index') }}" class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Language:</label>
            <select name="locale" onchange="this.form.submit()" class="border-gray-300 rounded text-sm">
                @foreach ($languages as $language)
                <option value="{{ $language->language }}" {{ $locale === $language->language ? 'selected' : '' }}>{{ $language->name }} ({{ $language->language }})</option>
                @endforeach
            </select>
        </form>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Add / Update Translation</h3>
            <p class="text-xs text-gray-500 mb-3">
                Key should be the exact English string as it appears in the app (e.g. <code>Register Free</code>). English itself needs no rows — untranslated keys automatically fall back to the key text.
            </p>
            <form method="POST" action="{{ route('admin.translations.store') }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <input type="hidden" name="locale" value="{{ $locale }}">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs text-gray-500 mb-1">Key (English)</label>
                    <input type="text" name="key" required class="border-gray-300 rounded text-sm w-full">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs text-gray-500 mb-1">Value ({{ $locale }})</label>
                    <input type="text" name="value" required class="border-gray-300 rounded text-sm w-full">
                </div>
                <button class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">Save</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($translations as $translation)
            <form method="POST" action="{{ route('admin.translations.update', $translation) }}" class="p-3 flex gap-3 items-center">
                @csrf @method('PUT')
                <div class="w-1/3 text-sm text-gray-600 truncate" title="{{ $translation->key }}">{{ $translation->key }}</div>
                <input type="text" name="value" value="{{ $translation->value }}" class="flex-1 border-gray-300 rounded text-sm">
                <button class="text-xs text-blue-600 hover:underline">Save</button>
            </form>
            @empty
            <p class="p-5 text-gray-400">No translations for this language yet.</p>
            @endforelse
        </div>

        {{ $translations->links() }}
    </div>
</x-admin-layout>
