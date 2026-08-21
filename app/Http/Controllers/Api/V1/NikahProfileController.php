<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Http\Controllers\Controller;
use App\Models\NikahProfile;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

// The app's Nikah profile wizard sends one screen's fields per call rather
// than the whole 30-field form at once — store() validates and saves just
// whatever's present (same partial-save shape as the web wizard's
// per-step saveStep(), but upserting the real row instead of PHP session
// state, since a token-authenticated mobile client has no server session
// to persist step state in between screens).
class NikahProfileController extends Controller
{
    use ValidatesNikahProfile;

    // The DB columns with no ->nullable()/->default() — a row literally
    // cannot be INSERTed without these, so the app's first wizard screen
    // must collect all four together before the very first store() call.
    private const CREATION_REQUIRED = ['age', 'city', 'guardian_name', 'guardian_contact'];

    // Human labels for field-name-interpolated messages below — translated
    // via __('db.') same as the message templates themselves, so e.g. a
    // Urdu speaker sees "شہر" rather than the raw column name "city".
    // Casing matters: the `translations` table's lookup is case-insensitive
    // on write (MySQL's default collation), so e.g. seeding a lowercase
    // 'age' silently updated a pre-existing 'Age' row from the website's
    // own forms rather than creating a separate one — these values must
    // match that existing casing exactly, or the __('db.') lookup (which
    // *is* case-sensitive, a plain PHP array key match) misses it.
    private const FIELD_LABELS = [
        'age' => 'Age',
        'city' => 'City',
        'guardian_name' => 'Guardian Name',
        'guardian_contact' => 'Guardian Contact Number',
        'cnic_number' => 'CNIC Number',
        'cnic_front_image' => 'CNIC front image',
        'cnic_back_image' => 'CNIC back image',
    ];

    private function fieldLabel(string $field): string
    {
        return __('db.' . (self::FIELD_LABELS[$field] ?? $field));
    }

    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->nikahProfile;

        if (!$profile) {
            return response()->json(['has_profile' => false]);
        }

        return response()->json(['has_profile' => true, 'profile' => $this->profilePayload($profile)]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->nikahProfile;

        if (!$profile) {
            $missing = array_diff(self::CREATION_REQUIRED, array_keys(array_filter($request->all())));
            if ($missing) {
                throw ValidationException::withMessages(
                    collect($missing)->mapWithKeys(fn ($f) => [$f => __('db.The :field field is required to create your profile.', ['field' => $this->fieldLabel($f)])])->toArray()
                );
            }
        }

        $submittedKeys = array_keys($request->all());

        // "Other" pairs (height/sect) and the multi-select language list
        // resolve down to a single stored column — pull in the rule for
        // whichever half of the pair the client actually sent.
        if ($request->has('height') || $request->has('height_other')) {
            $submittedKeys[] = 'height';
            $submittedKeys[] = 'height_other';
        }
        if ($request->has('sect') || $request->has('sect_other')) {
            $submittedKeys[] = 'sect';
            $submittedKeys[] = 'sect_other';
        }
        if ($request->has('languages') || $request->has('language_other')) {
            $submittedKeys[] = 'languages';
            $submittedKeys[] = 'languages.*';
            $submittedKeys[] = 'language_other';
        }

        $rules = collect($this->nikahProfileRules($profile))->only(array_unique($submittedKeys))->toArray();

        if ($request->has('gender')) {
            $rules['gender'] = ['required', 'in:male,female'];
        }

        $validated = $request->validate($rules, $this->nikahProfileMessages());

        if (array_key_exists('gender', $validated)) {
            if ($validated['gender'] !== $user->gender) {
                $user->update(['gender' => $validated['gender']]);
            }
            unset($validated['gender']);
        }

        if ($request->has('height') || $request->has('height_other')) {
            $validated['height'] = $this->resolveHeight($request);
            unset($validated['height_other']);
        }
        if ($request->has('sect') || $request->has('sect_other')) {
            $validated['sect'] = $this->resolveSect($request);
            unset($validated['sect_other']);
        }
        if ($request->has('languages') || $request->has('language_other')) {
            $validated['language'] = $this->resolveLanguage($request);
            unset($validated['languages'], $validated['language_other']);
        }
        if ($request->has('open_to_polygamy')) {
            $validated['open_to_polygamy'] = $request->boolean('open_to_polygamy');
        }
        if ($request->has('allow_photo_sharing')) {
            $validated['allow_photo_sharing'] = $request->boolean('allow_photo_sharing');
        }

        try {
            foreach (['cnic_front_image', 'cnic_back_image', 'photo'] as $file) {
                if ($request->hasFile($file)) {
                    $disk = $file === 'photo' ? 'nikah/photos' : 'nikah/cnic';
                    $maxDimension = $file === 'photo' ? 1200 : 1600;
                    $quality = $file === 'photo' ? 82 : 85;
                    $validated[$file] = ImageOptimizer::store($request->file($file), $disk, 'private', maxDimension: $maxDimension, quality: $quality);
                }
            }
        } catch (\Throwable $e) {
            report($e);
            throw ValidationException::withMessages(['photo' => __('db.Sorry, we could not save your uploaded photo(s) — please try again in a moment.')]);
        }

        if (!$profile) {
            $validated['user_id'] = $user->id;
            $validated['public_token'] = Str::random(32);
            $validated['payment_amount'] = setting('nikah_verification_fee', config('services.nikah.verification_fee'));
            $profile = NikahProfile::create($validated);

            if (!$user->city && !empty($validated['city'])) {
                $user->update(['city' => $validated['city']]);
            }
        } else {
            $profile->update($validated);
        }

        return response()->json(['profile' => $this->profilePayload($profile->fresh())], $profile->wasRecentlyCreated ? 201 : 200);
    }

    // A completeness gate the app calls before moving on to the payment
    // step — doesn't write anything itself, since every field is already
    // persisted incrementally by store() above.
    public function submit(Request $request): JsonResponse
    {
        $profile = $request->user()->nikahProfile;

        if (!$profile) {
            throw ValidationException::withMessages(['profile' => __('db.Create your profile first.')]);
        }

        $missing = collect(['cnic_number', 'cnic_front_image', 'cnic_back_image'])
            ->filter(fn ($field) => empty($profile->{$field}));

        if ($missing->isNotEmpty()) {
            throw ValidationException::withMessages(
                $missing->mapWithKeys(fn ($f) => [$f => __('db.Please provide your :field before submitting.', ['field' => $this->fieldLabel($f)])])->toArray()
            );
        }

        return response()->json([
            'message' => __('db.Profile submitted for verification.'),
            'profile' => $this->profilePayload($profile),
        ]);
    }

    public function profilePayload(NikahProfile $profile): array
    {
        return [
            'id' => $profile->id,
            'public_token' => $profile->public_token,
            'age' => $profile->age,
            'height' => $profile->height,
            'marital_status' => $profile->marital_status,
            'open_to_polygamy' => $profile->open_to_polygamy,
            'sect' => $profile->sect,
            'caste' => $profile->caste,
            'prayer_frequency' => $profile->prayer_frequency,
            'hijab_or_beard' => $profile->hijab_or_beard,
            'smokes' => $profile->smokes,
            'diet' => $profile->diet,
            'education' => $profile->education,
            'profession' => $profile->profession,
            'city' => $profile->city,
            'state' => $profile->state,
            'country' => $profile->country,
            'ethnicity' => $profile->ethnicity,
            'language' => $profile->language,
            'family_type' => $profile->family_type,
            'guardian_name' => $profile->guardian_name,
            'guardian_contact' => $profile->guardian_contact,
            'guardian_relation' => $profile->guardian_relation,
            'about' => $profile->about,
            'expectations' => $profile->expectations,
            'has_photo' => (bool) $profile->photo,
            'allow_photo_sharing' => $profile->allow_photo_sharing,
            'has_cnic_number' => (bool) $profile->cnic_number,
            'has_cnic_front_image' => (bool) $profile->cnic_front_image,
            'has_cnic_back_image' => (bool) $profile->cnic_back_image,
            'verification_status' => $profile->verification_status,
            'rejection_reason' => $profile->rejection_reason,
            'visibility' => $profile->visibility,
            'is_active' => $profile->is_active,
            'payment_status' => $profile->payment_status,
            'payment_amount' => $profile->payment_amount,
            'pref_min_age' => $profile->pref_min_age,
            'pref_max_age' => $profile->pref_max_age,
            'pref_city' => $profile->pref_city,
            'pref_sect' => $profile->pref_sect,
            'pref_education' => $profile->pref_education,
            'pref_marital_status' => $profile->pref_marital_status,
            'completeness_percentage' => $profile->completenessPercentage(),
            'can_browse' => $profile->payment_status === 'confirmed' && $profile->verification_status === 'verified',
        ];
    }
}
