@php $package = $package ?? null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input id="name" name="name" type="text" class="w-full mt-1" :value="old('name', $package?->name)" required />
    </div>
    <div>
        <x-input-label for="slug" value="Slug (used internally, no spaces)" />
        <x-text-input id="slug" name="slug" type="text" class="w-full mt-1" :value="old('slug', $package?->slug)" required />
    </div>
</div>

<div>
    <x-input-label for="name_ur" value="Name (Urdu — optional, falls back to English if blank)" />
    <x-text-input id="name_ur" name="name_ur" type="text" dir="rtl" class="w-full mt-1" :value="old('name_ur', $package?->name_ur)" />
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="tagline" value="Tagline (short one-liner shown on the pricing page)" />
        <x-text-input id="tagline" name="tagline" type="text" class="w-full mt-1" :value="old('tagline', $package?->tagline)" />
    </div>
    <div>
        <x-input-label for="tagline_ur" value="Tagline (Urdu — optional)" />
        <x-text-input id="tagline_ur" name="tagline_ur" type="text" dir="rtl" class="w-full mt-1" :value="old('tagline_ur', $package?->tagline_ur)" />
    </div>
</div>

<div class="grid grid-cols-3 gap-4">
    <div>
        <x-input-label for="price" value="Price (Rs.)" />
        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="w-full mt-1" :value="old('price', $package?->price)" required />
    </div>
    <div>
        <x-input-label for="duration_days" value="Duration (days, blank = one-time)" />
        <x-text-input id="duration_days" name="duration_days" type="number" min="1" class="w-full mt-1" :value="old('duration_days', $package?->duration_days)" />
    </div>
    <div>
        <x-input-label for="proposal_limit" value="Proposal Limit (blank = no cap)" />
        <x-text-input id="proposal_limit" name="proposal_limit" type="number" min="1" class="w-full mt-1" :value="old('proposal_limit', $package?->proposal_limit)" />
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="consultant_level" value="Consultant Level (short label, optional)" />
        <x-text-input id="consultant_level" name="consultant_level" type="text" class="w-full mt-1" :value="old('consultant_level', $package?->consultant_level)" />
    </div>
    <div>
        <x-input-label for="color" value="Color Badge" />
        <select id="color" name="color" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
            @foreach (\App\Http\Controllers\Admin\NikahPackageController::COLORS as $c)
            <option value="{{ $c }}" {{ old('color', $package?->color) === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="icon" value="Icon (one emoji, optional)" />
        <x-text-input id="icon" name="icon" type="text" class="w-full mt-1" maxlength="8" :value="old('icon', $package?->icon)" />
    </div>
    <div>
        <x-input-label for="sort_order" value="Display Order (lower shows first)" />
        <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="w-full mt-1" :value="old('sort_order', $package?->sort_order ?? 0)" />
    </div>
</div>

<div>
    <x-input-label for="description" value="Description (shown on the public pricing page)" />
    <input id="trix-description" type="hidden" name="description" value="{{ old('description', $package?->description) }}">
    <trix-editor input="trix-description"></trix-editor>
</div>

<div>
    <x-input-label for="description_ur" value="Description (Urdu — optional, falls back to English if blank)" />
    <input id="trix-description_ur" type="hidden" name="description_ur" value="{{ old('description_ur', $package?->description_ur) }}">
    <trix-editor input="trix-description_ur" dir="rtl"></trix-editor>
</div>

<div>
    <x-input-label for="features_text" value="Features (one per line — shown as a checklist on the pricing page)" />
    <textarea id="features_text" name="features_text" rows="8" class="border-gray-300 rounded-md shadow-sm w-full mt-1 font-mono text-sm">{{ old('features_text', $package ? implode("\n", $package->features ?? []) : '') }}</textarea>
</div>

<div>
    <x-input-label for="features_text_ur" value="Features (Urdu — one per line, same order as English; optional)" />
    <textarea id="features_text_ur" name="features_text_ur" dir="rtl" rows="8" class="border-gray-300 rounded-md shadow-sm w-full mt-1 text-sm">{{ old('features_text_ur', $package ? implode("\n", $package->features_ur ?? []) : '') }}</textarea>
</div>

<div class="flex gap-6">
    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package?->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600">
        Active (assignable to clients)
    </label>
    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="show_on_public_page" value="1" {{ old('show_on_public_page', $package?->show_on_public_page ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600">
        Show on public pricing page
    </label>
</div>
