@props(['name', 'id' => null, 'required' => false, 'allowGallery' => true, 'allowCamera' => true])

{{-- One real file input driving up to two clearly-labeled buttons — Upload
     from Gallery opens the normal file/photo picker, Take a Photo hints the
     camera. Both show by default (unchanged for every existing caller);
     a field that needs to force one path (e.g. a selfie that must be a
     live capture, never an uploaded file someone else took) passes
     :allow-gallery="false" or :allow-camera="false" to show just the one
     relevant button. --}}
<div x-data="{ fileName: '' }">
    <input type="file" id="{{ $id ?? $name }}" name="{{ $name }}" accept="image/*" x-ref="fileInput"
        @change="fileName = $event.target.files[0]?.name || ''"
        {{ $attributes->merge(['class' => 'sr-only']) }} {{ $required ? 'required' : '' }}>
    <div class="flex flex-col sm:flex-row gap-2">
        {{-- Clearing .value before toggling `capture` and reopening the
             picker (rather than mutating the attribute on an input that
             may already hold a selected file) avoids leaving a stale
             FileList behind — one contributor to Chrome's
             ERR_UPLOAD_FILE_CHANGED at submit time. --}}
        @if ($allowGallery)
        <button type="button" @click="$refs.fileInput.value = ''; $refs.fileInput.removeAttribute('capture'); $refs.fileInput.click()"
            class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
            📁 {{ __('db.Upload from Gallery') }}
        </button>
        @endif
        @if ($allowCamera)
        <button type="button" @click="$refs.fileInput.value = ''; $refs.fileInput.setAttribute('capture', 'environment'); $refs.fileInput.click()"
            class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
            📷 {{ __('db.Take a Photo') }}
        </button>
        @endif
    </div>
    <p class="text-xs text-teal-700 mt-1" x-show="fileName" x-cloak x-text="'✓ ' + fileName"></p>
</div>
