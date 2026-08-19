<?php

namespace App\Http\Controllers\Concerns;

use App\Models\NikahProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Lifted out of NikahProfileController unchanged so the single-page
// controller (edit/update) and the multi-step wizard controller
// (create) validate against the exact same rules — zero behavior drift.
trait ValidatesNikahProfile
{
    protected function nikahProfileRules(?NikahProfile $profile = null): array
    {
        return [
            'age' => ['required', 'integer', 'min:18', 'max:70'],
            'height' => ['nullable', 'string', 'max:20'],
            'height_other' => ['nullable', 'required_if:height,Other', 'string', 'max:30'],
            'marital_status' => ['required', 'string', 'in:never_married,divorced,widowed,married,separated'],
            'open_to_polygamy' => ['nullable', 'boolean'],
            'sect' => ['nullable', 'string', 'in:Sunni,Shia,Ahle Hadith,Deobandi,Other'],
            'sect_other' => ['nullable', 'required_if:sect,Other', 'string', 'max:100'],
            'caste' => ['nullable', 'string', 'max:100'],
            'prayer_frequency' => ['nullable', 'string', 'in:always,usually,sometimes,rarely'],
            'hijab_or_beard' => ['nullable', 'string', 'in:yes,no,sometimes'],
            'smokes' => ['nullable', 'string', 'in:no,occasionally,yes'],
            'diet' => ['nullable', 'string', 'in:halal_only,halal_mostly,no_restriction'],
            'education' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'ethnicity' => ['nullable', 'string', 'max:100'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['string', 'max:50'],
            'language_other' => ['nullable', 'string', 'max:100'],
            'family_type' => ['nullable', 'string', 'max:150'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'guardian_contact' => ['required', 'string', 'max:20'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'about' => ['nullable', 'string', 'max:2000'],
            'expectations' => ['nullable', 'string', 'max:2000'],
            'cnic_number' => [
                $profile?->cnic_number ? 'nullable' : 'required',
                'string',
                'max:20',
                Rule::unique('nikah_profiles', 'cnic_number')->ignore($profile?->id),
            ],
            'cnic_front_image' => [$profile?->cnic_front_image ? 'nullable' : 'required', 'image', 'max:4096'],
            'cnic_back_image' => [$profile?->cnic_back_image ? 'nullable' : 'required', 'image', 'max:4096'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'allow_photo_sharing' => ['nullable', 'boolean'],
            'visibility' => ['required', 'in:public,private'],
            'pref_min_age' => ['nullable', 'integer', 'min:18'],
            'pref_max_age' => ['nullable', 'integer', 'min:18'],
            'pref_city' => ['nullable', 'string', 'max:100'],
            'pref_sect' => ['nullable', 'string', 'max:100'],
            'pref_education' => ['nullable', 'string', 'max:100'],
            'pref_marital_status' => ['nullable', 'string'],
        ];
    }

    protected function nikahProfileMessages(): array
    {
        return [
            'cnic_number.unique' => 'This CNIC number is already registered to another profile.',
        ];
    }

    protected function validateProfile(Request $request, ?NikahProfile $profile = null): array
    {
        return $request->validate($this->nikahProfileRules($profile), $this->nikahProfileMessages());
    }

    // The "Other" sect option pairs with a free-text box (sect_other) that only
    // gets validated/shown when it's selected — resolve that pair down to the
    // single string that actually gets stored.
    protected function resolveSect(Request $request): ?string
    {
        if ($request->input('sect') === 'Other') {
            return $request->input('sect_other');
        }
        return $request->input('sect');
    }

    // Same "Other" pattern as sect above, for the height dropdown.
    protected function resolveHeight(Request $request): ?string
    {
        if ($request->input('height') === 'Other') {
            return $request->input('height_other');
        }
        return $request->input('height');
    }

    // Language is a multi-select checkbox group (languages[]) plus a free-text
    // "other" box for anything not in the common list — both collapse down to
    // the single comma-separated string the `language` column stores.
    protected function resolveLanguage(Request $request): ?string
    {
        $selected = collect($request->input('languages', []))->filter();

        if ($request->filled('language_other')) {
            $selected->push(trim($request->input('language_other')));
        }

        return $selected->isEmpty() ? null : $selected->implode(', ');
    }
}
