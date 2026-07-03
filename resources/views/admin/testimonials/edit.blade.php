<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.testimonials.index') }}" class="text-gray-400 hover:text-gray-600">Testimonials</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">New Testimonial</span>
        </div>
    </x-slot>
    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm p-6">
            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
            <form method="POST" action="{{ route('admin.testimonials.update', $testimonial->id) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div><x-input-label value="Full Name" /><x-text-input name="name" class="w-full mt-1" required /></div>
                    <div><x-input-label value="Location (City / Country)" /><x-text-input name="location" class="w-full mt-1" /></div>
                </div>
                <div>
                    <x-input-label value="Testimonial Content" />
                    <textarea name="content" rows="4" class="border-gray-300 rounded-md w-full mt-1" required></textarea>
                </div>
                <div>
                    <x-input-label value="Rating (1–5 stars)" />
                    <select name="rating" class="border-gray-300 rounded-md w-full mt-1">
                        @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ str_repeat('★', $i) }} {{ $i }} Stars</option>
                        @endfor
                    </select>
                </div>
                <div><x-input-label value="Photo (optional)" /><input type="file" name="photo" accept="image/*" class="w-full mt-1"></div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active" checked class="rounded">
                    <label for="is_active" class="text-sm">Show on homepage</label>
                </div>
                <x-primary-button>Update Testimonial</x-primary-button>
            </form>
        </div>
    </div>
</x-admin-layout>