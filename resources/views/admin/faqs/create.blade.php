<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.faqs.index') }}" class="text-gray-400 hover:text-gray-600">FAQs</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Add FAQ</span>
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

        <form method="POST" action="{{ route('admin.faqs.store') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label value="Module" />
                <select name="module" class="w-full mt-1 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" required>
                    <option value="">Choose a module…</option>
                    @foreach ($modules as $key => $label)
                    <option value="{{ $key }}" @selected(old('module') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label value="Sort order" />
                <x-text-input type="number" name="sort_order" class="w-32 mt-1" value="{{ old('sort_order', 0) }}" min="0" />
                <p class="text-xs text-gray-400 mt-1">Lower numbers show first within a module.</p>
            </div>

            <hr>

            <p class="text-sm font-semibold text-gray-600">🇬🇧 English</p>
            <div>
                <x-input-label value="Question (English)" />
                <x-text-input name="question_en" class="w-full mt-1" value="{{ old('question_en') }}" required />
            </div>
            <div>
                <x-input-label value="Answer (English)" />
                <textarea name="answer_en" rows="4" class="w-full mt-1 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" required>{{ old('answer_en') }}</textarea>
            </div>

            <hr>

            <p class="text-sm font-semibold text-gray-600">🇵🇰 اردو (Urdu)</p>
            <div>
                <x-input-label value="Question (Urdu)" />
                <x-text-input name="question_ur" class="w-full mt-1" dir="rtl" value="{{ old('question_ur') }}" />
            </div>
            <div>
                <x-input-label value="Answer (Urdu)" />
                <textarea name="answer_ur" rows="4" dir="rtl" class="w-full mt-1 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500">{{ old('answer_ur') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Leave blank to fall back to the English text for Urdu-locale visitors.</p>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" checked>
                <label for="is_active" class="text-sm text-gray-700">Show on the site</label>
            </div>

            <x-primary-button>Add FAQ</x-primary-button>
        </form>
    </div>
</x-admin-layout>
