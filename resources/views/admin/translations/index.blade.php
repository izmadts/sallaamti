<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Translations</span>
        </div>
    </x-slot>

    <div class="max-w-5xl space-y-4">

        @if (session('status'))
        <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-5 flex flex-wrap items-center gap-4">
            <form method="GET" action="{{ route('admin.translations.index') }}" class="flex items-center gap-3">
                <label for="locale" class="text-sm font-medium text-gray-600 shrink-0">Language</label>
                <select id="locale" name="locale" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full sm:w-56 focus:outline-none focus:border-teal-500 bg-white">
                    @foreach ($languages as $language)
                    <option value="{{ $language->language }}" {{ $locale === $language->language ? 'selected' : '' }}>{{ $language->name }} ({{ $language->language }})</option>
                    @endforeach
                </select>
                @if ($search)
                <input type="hidden" name="search" value="{{ $search }}">
                @endif
            </form>

            @if ($missingCount > 0)
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-700">
                🔴 {{ $missingCount }} missing — shown first below
            </span>
            @else
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                ✅ Everything's translated
            </span>
            @endif

            <form method="POST" action="{{ route('admin.translations.rescan') }}" class="ml-auto">
                @csrf
                <button class="text-xs font-medium text-gray-500 hover:text-teal-700 border border-gray-200 rounded-lg px-3 py-2 hover:border-teal-300 transition">
                    🔄 Rescan Codebase
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Add / Update Translation</h3>
            <p class="text-xs text-gray-500 mb-3">
                Key should be the exact English string as it appears in the app (e.g. <code>Register Free</code>). English itself needs no rows — untranslated keys automatically fall back to the key text. The table below already lists every <code>db.*</code> key found anywhere in the project (views and PHP code, including dynamically-generated messages) — use this form only for a key the scan missed.
            </p>
            <form method="POST" action="{{ route('admin.translations.store') }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <input type="hidden" name="locale" value="{{ $locale }}">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs text-gray-500 mb-1">Key (English)</label>
                    <input type="text" name="key" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs text-gray-500 mb-1">Value ({{ $locale }})</label>
                    <input type="text" name="value" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                </div>
                <button class="bg-teal-700 text-white text-sm px-4 py-2.5 rounded-lg hover:bg-teal-800">Save</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <form method="GET" action="{{ route('admin.translations.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="locale" value="{{ $locale }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search key or translated text…"
                    class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                <button class="bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded-lg hover:bg-gray-200">Search</button>
                @if ($search)
                <a href="{{ route('admin.translations.index', ['locale' => $locale]) }}" class="text-xs text-gray-400 hover:text-gray-600">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($translations as $row)
            <form method="POST"
                action="{{ $row->id ? route('admin.translations.update', $row->id) : route('admin.translations.store') }}"
                class="p-3 flex flex-wrap gap-3 items-center {{ $row->missing ? 'bg-red-50/60' : '' }}">
                @csrf
                @if ($row->id)
                @method('PUT')
                @else
                <input type="hidden" name="locale" value="{{ $locale }}">
                <input type="hidden" name="group" value="{{ $row->group }}">
                <input type="hidden" name="key" value="{{ $row->key }}">
                @endif

                <div class="w-full sm:w-2/5 min-w-0">
                    <div class="text-sm text-gray-700 truncate" title="{{ $row->key }}">{{ $row->key }}</div>
                    @if ($row->missing)
                    <span class="text-[10px] font-semibold uppercase tracking-wide text-red-500">Missing</span>
                    @endif
                </div>
                <input type="text" name="value" value="{{ $row->value }}" placeholder="{{ $row->missing ? 'Not yet translated' : '' }}"
                    class="flex-1 min-w-[160px] border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500 {{ $row->missing ? 'border-red-200' : 'border-gray-200' }}">
                <button class="text-xs font-medium text-teal-700 hover:underline shrink-0">Save</button>
            </form>
            @empty
            <p class="p-5 text-gray-400">No translation keys found{{ $search ? ' matching your search' : '' }}.</p>
            @endforelse
        </div>

        {{ $translations->links() }}
    </div>
</x-admin-layout>
