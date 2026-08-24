<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Concerns\SubmitsNikahPayment;
use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\MatchmakingTimelineEvent;
use App\Models\NikahProfile;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

// Stateless mobile counterpart to Admin\NikahProfileWizardController's
// session-backed 7-step wizard, scoped by Lead instead of $user->nikahProfile
// — mirrors Api\V1\NikahProfileController's own single-partial-upsert
// pattern exactly (see that controller's class docblock), since a
// bearer-token client has no PHP session to persist step state in between
// screens either way.
class ClientProfileController extends Controller
{
    use ValidatesNikahProfile, RegistersMinimalUsers, SubmitsNikahPayment;

    private const CREATION_REQUIRED = ['date_of_birth', 'city', 'guardian_name', 'guardian_contact'];

    private const FIELD_LABELS = [
        'date_of_birth' => 'Date of Birth',
        'city' => 'City',
        'guardian_name' => 'Guardian Name',
        'guardian_contact' => 'Guardian Contact Number',
        'cnic_number' => 'CNIC Number',
        'cnic_front_image' => 'CNIC front image',
        'cnic_back_image' => 'CNIC back image',
        'identifier' => 'Email or Phone',
    ];

    private function fieldLabel(string $field): string
    {
        return __('db.' . (self::FIELD_LABELS[$field] ?? $field));
    }

    private function authorizeLead(Lead $lead): void
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->can('leads.manage')) {
            return;
        }

        abort_unless($lead->assigned_to === auth()->id(), 403, 'This client is assigned to another matchmaker.');
    }

    public function show(Lead $lead): JsonResponse
    {
        $this->authorizeLead($lead);

        $profile = $lead->nikahProfile;

        if (!$profile) {
            return response()->json([
                'has_profile' => false,
                'prefill' => [
                    'name' => $lead->name,
                    'phone' => $lead->phone,
                    'email' => $lead->email,
                    'gender' => $lead->gender,
                ],
            ]);
        }

        return response()->json(['has_profile' => true, 'profile' => $this->profilePayload($profile)]);
    }

    public function store(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeLead($lead);

        $profile = $lead->nikahProfile;

        if (!$profile) {
            $missing = array_diff(self::CREATION_REQUIRED, array_keys(array_filter($request->all())));
            if (blank($request->input('identifier'))) {
                $missing[] = 'identifier';
            }
            if ($missing) {
                throw ValidationException::withMessages(
                    collect($missing)->mapWithKeys(fn ($f) => [$f => __('db.The :field field is required to create this profile.', ['field' => $this->fieldLabel($f)])])->toArray()
                );
            }

            $identifier = (string) $request->input('identifier');
            $isEmail = str_contains($identifier, '@');
            $request->validate([
                'identifier' => [$isEmail ? 'email' : 'string', $isEmail ? 'unique:users,email' : 'unique:users,phone'],
            ], [], ['identifier' => $this->fieldLabel('identifier')]);
        }

        $submittedKeys = array_keys($request->all());

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

        $gender = $validated['gender'] ?? null;
        unset($validated['gender']);

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
            throw ValidationException::withMessages(['photo' => __('db.Sorry, we could not save the uploaded photo(s) — please try again in a moment.')]);
        }

        if (!$profile) {
            $identifier = (string) $request->input('identifier');
            $isEmail = str_contains($identifier, '@');

            $user = $this->createMinimalUser(
                $lead->name,
                $isEmail ? $identifier : null,
                $isEmail ? null : $identifier,
            );
            if ($gender) {
                $user->update(['gender' => $gender]);
            }

            $validated['user_id'] = $user->id;
            $validated['public_token'] = Str::random(32);
            $validated['created_by'] = auth()->id();
            $validated['payment_amount'] = NikahProfile::feeForMaritalStatus($validated['marital_status'] ?? null);
            $profile = NikahProfile::create($validated);

            $lead->update(['nikah_profile_id' => $profile->id, 'status' => 'registered']);
            MatchmakingTimelineEvent::log($lead, $profile, 'registration_started', 'Matchmaker started assisted registration.');
        } else {
            if ($gender && $profile->user) {
                $profile->user->update(['gender' => $gender]);
            }
            $profile->update($validated);
        }

        return response()->json(['profile' => $this->profilePayload($profile->fresh())], $profile->wasRecentlyCreated ? 201 : 200);
    }

    public function payment(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeLead($lead);

        $profile = $lead->nikahProfile;
        abort_unless($profile, 422, 'This client has no linked Nikah profile yet.');
        abort_if($profile->payment_status === 'confirmed', 422, 'Payment is already confirmed for this profile.');

        $fee = $profile->applicableVerificationFee();
        abort_if($fee <= 0, 422, 'No verification fee applies to this profile — nothing to submit.');

        $validated = $request->validate([
            'payment_method' => ['required', 'in:jazzcash,bank_transfer,cash,whatsapp,other'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'payment_screenshot' => ['required', 'image', 'max:4096'],
        ]);

        $screenshotPath = ImageOptimizer::store($request->file('payment_screenshot'), 'nikah/payments', 'private');

        $this->recordNikahPaymentSubmission($profile, $validated['payment_method'], $validated['payment_reference'] ?? null, $screenshotPath);

        MatchmakingTimelineEvent::log($lead, $profile, 'payment_submitted', 'Payment proof submitted for admin review.');

        return response()->json(['message' => __('db.Payment proof submitted — our team will confirm it shortly.'), 'profile' => $this->profilePayload($profile->fresh())]);
    }

    private function profilePayload(NikahProfile $profile): array
    {
        return app(\App\Http\Controllers\Api\V1\NikahProfileController::class)->profilePayload($profile);
    }
}
