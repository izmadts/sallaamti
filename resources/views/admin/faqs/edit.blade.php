<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.faqs.index') }}" class="text-gray-400 hover:text-gray-600">FAQs</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Edit FAQ</span>
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

        <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <x-input-label value="Module" />
                <select name="module" class="w-full mt-1 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" required>
                    @foreach ($modules as $key => $label)
                    <option value="{{ $key }}" @selected(old('module', $faq->module) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label value="Sort order" />
                <x-text-input type="number" name="sort_order" class="w-32 mt-1" value="{{ old('sort_order', $faq->sort_order) }}" min="0" />
                <p class="text-xs text-gray-400 mt-1">Lower numbers show first within a module.</p>
            </div>

            <hr>

            <p class="text-sm font-semibold text-gray-600">🇬🇧 English</p>
            <div>
                <x-input-label value="Question (English)" />
                <x-text-input name="question_en" class="w-full mt-1" value="{{ old('question_en', $faq->question_en) }}" required />
            </div>
            <div>
                <x-input-label value="Answer (English)" />
                <input id="trix-answer_en" type="hidden" name="answer_en" value="{{ old('answer_en', $faq->answer_en) }}">
                <trix-editor input="trix-answer_en"></trix-editor>
            </div>

            <hr>

            <p class="text-sm font-semibold text-gray-600">🇵🇰 اردو (Urdu)</p>
            <div>
                <x-input-label value="Question (Urdu)" />
                <x-text-input name="question_ur" class="w-full mt-1" dir="rtl" value="{{ old('question_ur', $faq->question_ur) }}" />
            </div>
            <div>
                <x-input-label value="Answer (Urdu)" />
                <input id="trix-answer_ur" type="hidden" name="answer_ur" value="{{ old('answer_ur', $faq->answer_ur) }}">
                <trix-editor input="trix-answer_ur" dir="rtl"></trix-editor>
                <p class="text-xs text-gray-400 mt-1">Leave blank to fall back to the English text for Urdu-locale visitors.</p>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $faq->is_active))>
                <label for="is_active" class="text-sm text-gray-700">Show on the site</label>
            </div>

            <x-primary-button>Save Changes</x-primary-button>
        </form>
    </div>
</x-admin-layout>
