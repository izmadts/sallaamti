<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.daily-content.index') }}" class="text-gray-400 hover:text-gray-600">Daily Ayah / Hadith</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Add Entry</span>
        </div>
    </x-slot>

    <div class="max-w-2xl bg-white rounded-lg shadow-sm p-6">
        @if ($errors->any())
        <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.daily-content.store') }}" class="space-y-4">
            @csrf
            <div>
                <x-input-label value="Type" />
                <select name="type" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    <option value="ayah" {{ old('type') === 'ayah' ? 'selected' : '' }}>Ayah (Quran verse)</option>
                    <option value="hadith" {{ old('type') === 'hadith' ? 'selected' : '' }}>Hadith</option>
                </select>
            </div>
            <div>
                <x-input-label value="Arabic Text (optional)" />
                <textarea name="arabic_text" rows="2" dir="rtl" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">{{ old('arabic_text') }}</textarea>
            </div>
            <div>
                <x-input-label value="Translation" />
                <input id="trix-translation" type="hidden" name="translation" value="{{ old('translation') }}">
                <trix-editor input="trix-translation" placeholder="The English translation/text shown to members"></trix-editor>
            </div>
            <div>
                <x-input-label value="Reference" />
                <x-text-input name="reference" class="w-full mt-1" value="{{ old('reference') }}" placeholder="e.g. Quran 2:153, or Sahih Bukhari 1" required />
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" checked>
                <label for="is_active" class="text-sm text-gray-700">Include in the daily rotation</label>
            </div>
            <x-primary-button>Add Entry</x-primary-button>
        </form>
    </div>
</x-admin-layout>
