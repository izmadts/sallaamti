<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Concerns\HasWizardSteps;
use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Http\Controllers\Controller;
use App\Models\NikahProfile;
use App\Models\User;
use App\Rules\ValidPhoneNumber;
use App\Services\ImageOptimizer;
use App\Support\CountryStates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// Admin/matchmaker walk-in data entry, as a step wizard — same
// HasWizardSteps session-per-step pattern as the member-facing
// NikahProfileWizardController, plus a leading "account" step for the
// name/identifier/gender that only exists here (walk-ins don't have an
// account yet). Held to the exact same validation (incl. required CNIC
// photos) as a member creating their own profile — this is in-person,
// consented data entry, not the same thing as a matchmaker browsing an
// existing member's CNIC/photo without their knowledge (see the
// Matchmaker browse controller, which redacts both).
class NikahProfileWizardController extends Controller
{
    use HasWizardSteps, ValidatesNikahProfile, RegistersMinimalUsers;

    protected string $wizardKey = 'admin_nikah_profile';

    protected array $wizardStepFields = [
        'account' => ['name', 'identifier', 'gender'],
        'basic' => ['age', 'height', 'height_other', 'marital_status', 'education', 'profession', 'city', 'state', 'country'],
        'family' => ['caste', 'family_type', 'guardian_name', 'guardian_contact', 'guardian_relation', 'ethnicity'],
        'deen' => ['sect', 'sect_other', 'prayer_frequency', 'hijab_or_beard', 'smokes', 'diet', 'open_to_polygamy'],
        'about' => ['about', 'expectations'],
        'verification' => ['cnic_number', 'cnic_front_image', 'cnic_back_image', 'photo', 'allow_photo_sharing', 'visibility'],
    ];

    protected array $stepTitles = [
        'account' => 'Login Account',
        'basic' => 'Basic Info',
        'family' => 'Family & Guardian',
        'deen' => 'Deen & Lifestyle',
        'about' => 'About',
        'verification' => 'Verification & Visibility',
    ];

    // Deliberately checked in-controller rather than route middleware —
    // nikah.create-profile is narrower than nikah.manage (matchmaker has
    // only the former), and Laravel's `can:` route middleware has no
    // built-in OR between two separate abilities.
    private function authorizeProfileCreation(): void
    {
        abort_unless(auth()->user()->can('nikah.manage') || auth()->user()->can('nikah.create-profile'), 403);
    }

    public function start()
    {
        $this->authorizeProfileCreation();

        $step = $this->firstIncompleteWizardStep();

        return $step
            ? redirect()->route('admin.nikah.profiles.create.step', $step)
            : redirect()->route('admin.nikah.profiles.create.review');
    }

    public function showStep(string $step)
    {
        $this->authorizeProfileCreation();

        abort_unless(array_key_exists($step, $this->wizardStepFields), 404);

        return view("admin.nikah.wizard.step-{$step}", [
            'step' => $step,
            'steps' => $this->wizardSteps(),
            'stepTitles' => $this->stepTitles,
            'data' => $this->wizardStepData($step),
            'countries' => $step === 'basic' ? CountryStates::countries() : [],
            'countryStates' => $step === 'basic' ? CountryStates::map() : [],
        ]);
    }

    public function saveStep(Request $request, string $step)
    {
        $this->authorizeProfileCreation();

        abort_unless(array_key_exists($step, $this->wizardStepFields), 404);

        if ($step === 'account') {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'identifier' => ['required', 'string', 'max:255'],
                'gender' => ['required', 'in:male,female'],
            ]);

            $identifier = trim($validated['identifier']);
            $isEmail = (bool) filter_var($identifier, FILTER_VALIDATE_EMAIL);
            if (!$isEmail) {
                $identifier = preg_replace('/[\s\-]/', '', $identifier);
            }

            Validator::make(['identifier' => $identifier], [
                'identifier' => [
                    $isEmail ? 'email' : new ValidPhoneNumber(),
                    Rule::unique(User::class, $isEmail ? 'email' : 'phone'),
                ],
            ])->validate();

            $validated['identifier'] = $identifier;
            $this->saveWizardStep('account', $validated);

            return $this->nextStepRedirect('account');
        }

        $rules = collect($this->nikahProfileRules())->only($this->wizardStepFields[$step])->toArray();
        $existing = $this->wizardStepData($step);

        if ($step === 'verification') {
            if (!empty($existing['cnic_front_image'])) {
                $rules['cnic_front_image'] = ['nullable', 'image', 'max:4096'];
            }
            if (!empty($existing['cnic_back_image'])) {
                $rules['cnic_back_image'] = ['nullable', 'image', 'max:4096'];
            }
        }

        $validated = $request->validate($rules, $this->nikahProfileMessages());

        if ($step === 'basic') {
            $validated['height'] = $this->resolveHeight($request);
        }

        if ($step === 'deen') {
            $validated['sect'] = $this->resolveSect($request);
            $validated['open_to_polygamy'] = $request->boolean('open_to_polygamy');
        }

        if ($step === 'verification') {
            try {
                foreach (['cnic_front_image', 'cnic_back_image', 'photo'] as $file) {
                    if ($request->hasFile($file)) {
                        $disk = $file === 'photo' ? 'nikah/photos' : 'nikah/cnic';
                        $maxDimension = $file === 'photo' ? 1200 : 1600;
                        $quality = $file === 'photo' ? 82 : 85;
                        $validated[$file] = ImageOptimizer::store($request->file($file), $disk, 'private', maxDimension: $maxDimension, quality: $quality);
                    } elseif (!empty($existing[$file])) {
                        $validated[$file] = $existing[$file];
                    }
                }
            } catch (\Throwable $e) {
                report($e);
                $message = 'Sorry, we could not save the uploaded photo(s) — please try again in a moment.';

                if ($request->wantsJson()) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['photo' => $message]);
                }

                return back()->withInput()->withErrors(['photo' => $message]);
            }

            $validated['allow_photo_sharing'] = $request->boolean('allow_photo_sharing');
        }

        $this->saveWizardStep($step, $validated);

        return $this->nextStepRedirect($step, $request);
    }

    // Accepts the request so the verification step's fetch()-based submit
    // (see admin/nikah/wizard/step-verification's script) gets a JSON
    // redirect instead of a real 302 — turns a browser-level upload
    // failure (e.g. Chrome's ERR_UPLOAD_FILE_CHANGED) into this page's own
    // friendly in-place error instead of Chrome's network-error screen.
    // Every other step still gets the plain redirect since only that
    // view's form actually sends the AJAX headers.
    private function nextStepRedirect(string $step, ?Request $request = null)
    {
        $steps = $this->wizardSteps();
        $nextIndex = array_search($step, $steps) + 1;

        $redirectUrl = $nextIndex < count($steps)
            ? route('admin.nikah.profiles.create.step', $steps[$nextIndex])
            : route('admin.nikah.profiles.create.review');

        if ($request?->wantsJson()) {
            return response()->json(['redirect' => $redirectUrl]);
        }

        return redirect($redirectUrl);
    }

    public function review()
    {
        $this->authorizeProfileCreation();

        if ($incomplete = $this->firstIncompleteWizardStep()) {
            return redirect()->route('admin.nikah.profiles.create.step', $incomplete);
        }

        return view('admin.nikah.wizard.review', [
            'steps' => $this->wizardSteps(),
            'stepTitles' => $this->stepTitles,
            'data' => $this->wizardAllData(),
        ]);
    }

    public function finalize()
    {
        $this->authorizeProfileCreation();

        if ($this->firstIncompleteWizardStep()) {
            return redirect()->route('admin.nikah.profiles.create');
        }

        $data = $this->wizardAllData();
        $accountData = collect($data)->only(['name', 'identifier', 'gender'])->toArray();
        $profileData = collect($data)->except(['name', 'identifier', 'gender'])->toArray();

        $identifier = $accountData['identifier'];
        $isEmail = (bool) filter_var($identifier, FILTER_VALIDATE_EMAIL);

        $user = $this->createMinimalUser(
            $accountData['name'],
            $isEmail ? strtolower($identifier) : null,
            $isEmail ? null : $identifier,
        );
        $user->update(['gender' => $accountData['gender'], 'city' => $profileData['city'] ?? null]);

        $profileData['user_id'] = $user->id;
        $profileData['allow_photo_sharing'] = (bool) ($profileData['allow_photo_sharing'] ?? false);
        $profileData['open_to_polygamy'] = (bool) ($profileData['open_to_polygamy'] ?? false);
        $profileData['payment_amount'] = setting('nikah_verification_fee', config('services.nikah.verification_fee'));
        $profileData['public_token'] = Str::random(32);

        $profile = NikahProfile::create($profileData);

        $status = "Profile created for {$user->name}.";

        if ($isEmail) {
            Password::sendResetLink(['email' => $user->email]);
            $status .= ' A password-setup link was emailed to them so they can log in.';
        } else {
            $status .= ' No email was provided, so no login link could be sent — add one to their account later via Users, then use "Send Password Reset Link" there.';
        }

        $this->clearWizardSession();

        return redirect()->route('admin.nikah.show', $profile)->with('status', $status);
    }
}
