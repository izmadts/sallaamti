@props(['name', 'id' => null, 'required' => false])

{{-- One real file input driving two clearly-labeled buttons — Upload from
     Gallery opens the normal file/photo picker, Take a Photo hints the
     camera — so both ways of getting a CNIC photo onto the form are
     always explicitly on offer, regardless of how a given phone's
     browser would otherwise have handled a bare capture="" input. --}}
<div x-data="{ fileName: '' }">
    <input type="file" id="{{ $id ?? $name }}" name="{{ $name }}" accept="image/*" x-ref="fileInput"
        @change="fileName = $event.target.files[0]?.name || ''"
        {{ $attributes->merge(['class' => 'sr-only']) }} {{ $required ? 'required' : '' }}>
    <div class="flex flex-col sm:flex-row gap-2">
        <button type="button" @click="$refs.fileInput.removeAttribute('capture'); $refs.fileInput.click()"
            class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
            📁 {{ __('db.Upload from Gallery') }}
        </button>
        <button type="button" @click="$refs.fileInput.setAttribute('capture', 'environment'); $refs.fileInput.click()"
            class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
            📷 {{ __('db.Take a Photo') }}
        </button>
    </div>
    <p class="text-xs text-teal-700 mt-1" x-show="fileName" x-cloak x-text="'✓ ' + fileName"></p>
</div>
