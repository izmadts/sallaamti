<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.library.index') }}" class="text-gray-400 hover:text-gray-600">Library</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ $item->exists ? 'Edit' : 'New' }}</span>
        </div>
    </x-slot>
    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm p-6">
            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm"><ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ $item->exists ? route('admin.library.update', $item) : route('admin.library.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @if ($item->exists) @method('PUT') @endif
                <div class="grid sm:grid-cols-2 gap-4">
                    <div><x-input-label value="Code (e.g. BK1)" /><x-text-input name="code" class="w-full mt-1" :value="old('code', $item->code)" /></div>
                    <div>
                        <x-input-label value="Section" />
                        <select name="category" class="border-gray-300 rounded-md w-full mt-1">
                            @foreach ($categories as $key => $cat)
                            <option value="{{ $key }}" {{ old('category', $item->category) === $key ? 'selected' : '' }}>{{ $cat['en'] }} — {{ $cat['ur'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div><x-input-label value="Title (Urdu)" /><x-text-input name="title" dir="rtl" class="w-full mt-1" :value="old('title', $item->title)" required /></div>
                <div><x-input-label value="Short description (shown on the card and in Google)" /><textarea name="description" dir="rtl" rows="3" class="border-gray-300 rounded-md w-full mt-1">{{ old('description', $item->description) }}</textarea></div>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div><x-input-label value="Pages" /><x-text-input name="pages" type="number" min="1" class="w-full mt-1" :value="old('pages', $item->pages)" /></div>
                    <div>
                        <x-input-label value="Language" />
                        <select name="language" class="border-gray-300 rounded-md w-full mt-1">
                            <option value="ur" {{ old('language', $item->language) === 'ur' ? 'selected' : '' }}>Urdu</option>
                            <option value="en" {{ old('language', $item->language) === 'en' ? 'selected' : '' }}>English</option>
                        </select>
                    </div>
                    <div><x-input-label value="Order" /><x-text-input name="sort_order" type="number" min="0" class="w-full mt-1" :value="old('sort_order', $item->sort_order)" /></div>
                </div>
                <div><x-input-label value="URL slug (optional, English letters)" /><x-text-input name="slug" class="w-full mt-1" :value="old('slug', $item->slug)" placeholder="e.g. walidain-booklet" /></div>
                <div>
                    <x-input-label :value="$item->exists ? 'Replace PDF (optional, max 20 MB)' : 'PDF file (max 20 MB)'" />
                    <input type="file" name="file" accept="application/pdf" class="w-full mt-1" {{ $item->exists ? '' : 'required' }}>
                    @if ($item->exists)<a href="{{ $item->fileUrl() }}" target="_blank" class="text-xs text-teal-700">Current PDF</a>@endif
                </div>
                <div>
                    <x-input-label value="Cover image (optional; without it the title is shown on green)" />
                    <input type="file" name="cover" accept="image/*" class="w-full mt-1">
                    @if ($item->coverUrl())<img src="{{ $item->coverUrl() }}" alt="" class="mt-2 rounded" style="height:90px">@endif
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" value="1" id="is_published" class="rounded" {{ old('is_published', $item->is_published) ? 'checked' : '' }}>
                    <label for="is_published" class="text-sm">Published (visible on /library)</label>
                </div>
                <x-primary-button>{{ $item->exists ? 'Save' : 'Add to library' }}</x-primary-button>
            </form>
        </div>
    </div>
</x-admin-layout>
