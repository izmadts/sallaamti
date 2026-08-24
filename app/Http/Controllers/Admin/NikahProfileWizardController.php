<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Concerns\HasWizardSteps;
use App\Http\Controllers\Concerns\SubmitsNikahPayment;
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
    use HasWizardSteps, ValidatesNikahProfile, RegistersMinimalUsers, SubmitsNikahPayment;

    protected string $wizardKey = 'admin_nikah_profile';

    protected array $wizardStepFields = [
        'account' => ['name', 'identifier', 'gender'],
        'basic' => ['date_of_birth', 'height', 'height_other', 'marital_status', 'has_children', 'children_count', 'living_situation', 'education', 'profession', 'city', 'state', 'country'],
        'family' => ['caste', 'family_type', 'guardian_name', 'guardian_contact', 'guardian_relation', 'ethnicity'],
        'deen' => ['sect', 'sect_other', 'prayer_frequency', 'hijab_or_beard', 'smokes', 'diet', 'open_to_polygamy'],
        'about' => ['about', 'expectations'],
        'verification' => ['cnic_number', 'cnic_front_image', 'cnic_back_image', 'photo', 'allow_photo_sharing', 'visibility', 'remote_verification'],
        // Optional — a matchmaker collecting payment on the spot can submit
        // it right here so the whole document set (CNIC + payment) is
        // reviewed by admin together; skippable if the client isn't paying
        // today (they can pay later themselves, or it can be submitted
        // afterward from the profile page — see Matchmaker\
        // NikahBrowseController::submitPayment()).
        'payment' => ['payment_method', 'payment_reference', 'payment_screenshot'],
    ];

    protected array $stepTitles = [
        'account' => 'Login Account',
        'basic' => 'Basic Info',
        'family' => 'Family & Guardian',
        'deen' => 'Deen & Lifestyle',
        'about' => 'About',
        'verification' => 'Verification & Visibility',
        'payment' => 'Payment',
    ];

    // Deliberately checked in-controller rather than route middleware —
    // nikah.create-profile is narrower than nikah.manage (matchmaker has
    // only the former), and Laravel's `can:` route middleware has no
    // built-in OR between two separate abilities.
    private function authorizeProfileCreation(): void
    {
        abort_unless(auth()->user()->can('nikah.manage') || auth()->user()->can('nikah.create-profile'), 403);
    }

    // This one controller is reached through two separate route families —
    // admin.nikah.profiles.create.* (/admin/...) and
    // matchmaker.nikah.profiles.create.* (/matchmaker/...), same
    // authorization either way (see authorizeProfileCreation() above).
    // Every internal redirect/link needs to stay on whichever family the
    // request actually came in on, or a matchmaker using their own branded
    // URL would get bounced into /admin/... mid-wizard.
    private function routeNamePrefix(): string
    {
        return str_starts_with(request()->route()->getName(), 'matchmaker.') ? 'matchmaker' : 'admin';
    }

    public function start()
    {
        $this->authorizeProfileCreation();

        $step = $this->firstIncompleteWizardStep();
        $prefix = $this->routeNamePrefix();

        return $step
            ? redirect()->route("{$prefix}.nikah.profiles.create.step", $step)
            : redirect()->route("{$prefix}.nikah.profiles.create.review");
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
            'routePrefix' => $this->routeNamePrefix(),
            'expectedFee' => $step === 'payment' ? NikahProfile::feeForMaritalStatus($this->wizardStepData('basic')['marital_status'] ?? null) : null,
        ]);
    }

    public function saveStep(Request $request, string $step)
    {
        $this->authorizeProfileCreation();

        abort_unless(array_key_exists($step, $this->wizardStepFields), 404);

        if ($step === 'payment') {
            $validated = $request->validate([
                'payment_method' => ['nullable', 'required_with:payment_screenshot', 'in:jazzcash,bank_transfer'],
                'payment_reference' => ['nullable', 'string', 'max:100'],
                'payment_screenshot' => ['nullable', 'image', 'max:4096'],
            ]);

            if ($request->hasFile('payment_screenshot')) {
                $validated['payment_screenshot'] = ImageOptimizer::store($request->file('payment_screenshot'), 'nikah/payments', 'private');
            } else {
                $validated['payment_screenshot'] = $this->wizardStepData('payment')['payment_screenshot'] ?? null;
            }

            $this->saveWizardStep('payment', $validated);

            return $this->nextStepRedirect('payment');
        }

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

            $rules['remote_verification'] = ['nullable', 'boolean'];

            // A matchmaker registering someone they can't physically meet
            // shouldn't be forced to collect CNIC/photo over WhatsApp just
            // to get past this step (see project_matchmaker_hiring_document
            // §20 — "no CNIC over WhatsApp"). Checking this box defers
            // collection to a secure self-upload the client does
            // themselves from their own progress link (see finalize()).
            if ($request->boolean('remote_verification')) {
                $rules['cnic_number'] = ['nullable', 'string', 'max:20'];
                $rules['cnic_front_image'] = ['nullable', 'image', 'max:4096'];
                $rules['cnic_back_image'] = ['nullable', 'image', 'max:4096'];
            }
        }

        $validated = $request->validate($rules, $this->nikahProfileMessages());

        if ($step === 'verification') {
            $validated['remote_verification'] = $request->boolean('remote_verification');
        }

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
        $prefix = $this->routeNamePrefix();

        $redirectUrl = $nextIndex < count($steps)
            ? route("{$prefix}.nikah.profiles.create.step", $steps[$nextIndex])
            : route("{$prefix}.nikah.profiles.create.review");

        if ($request?->wantsJson()) {
            return response()->json(['redirect' => $redirectUrl]);
        }

        return redirect($redirectUrl);
    }

    public function review()
    {
        $this->authorizeProfileCreation();
        $prefix = $this->routeNamePrefix();

        if ($incomplete = $this->firstIncompleteWizardStep()) {
            return redirect()->route("{$prefix}.nikah.profiles.create.step", $incomplete);
        }

        return view('admin.nikah.wizard.review', [
            'steps' => $this->wizardSteps(),
            'stepTitles' => $this->stepTitles,
            'data' => $this->wizardAllData(),
            'routePrefix' => $prefix,
        ]);
    }

    public function finalize()
    {
        $this->authorizeProfileCreation();

        if ($this->firstIncompleteWizardStep()) {
            return redirect()->route("{$this->routeNamePrefix()}.nikah.profiles.create");
        }

        $data = $this->wizardAllData();
        $accountData = collect($data)->only(['name', 'identifier', 'gender'])->toArray();
        $paymentData = collect($data)->only(['payment_method', 'payment_reference', 'payment_screenshot'])->toArray();
        $remoteVerification = (bool) ($data['remote_verification'] ?? false);
        // Payment fields are handled separately below via
        // recordNikahPaymentSubmission() (sets payment_status/amount
        // together, not left to mass-assignment) — excluded here so
        // create() doesn't half-apply them without a status.
        // remote_verification is wizard-only bookkeeping, not a real
        // NikahProfile column.
        $profileData = collect($data)->except(['name', 'identifier', 'gender', 'payment_method', 'payment_reference', 'payment_screenshot', 'remote_verification'])->toArray();

        $identifier = $accountData['identifier'];
        $isEmail = (bool) filter_var($identifier, FILTER_VALIDATE_EMAIL);

        $user = $this->createMinimalUser(
            $accountData['name'],
            $isEmail ? strtolower($identifier) : null,
            $isEmail ? null : $identifier,
        );
        $user->update(['gender' => $accountData['gender'], 'city' => $profileData['city'] ?? null]);

        $profileData['user_id'] = $user->id;
        $profileData['created_by'] = auth()->id();
        $profileData['allow_photo_sharing'] = (bool) ($profileData['allow_photo_sharing'] ?? false);
        $profileData['open_to_polygamy'] = (bool) ($profileData['open_to_polygamy'] ?? false);
        $profileData['payment_amount'] = NikahProfile::feeForMaritalStatus($profileData['marital_status'] ?? null);
        $profileData['public_token'] = Str::random(32);

        $profile = NikahProfile::create($profileData);

        // Link (or create) the Lead this profile belongs to — needed so a
        // remote-verification self-upload has a WhatsApp number and a
        // progress link to be gated by. If the wizard was reached via
        // ClientController::convert(), that Lead is reused (and, as a side
        // fix, actually gets linked back now — previously the matchmaker
        // had to do that manually afterward). Otherwise, only when remote
        // verification was actually requested, a minimal Lead is created
        // so the "one link for everything" mechanism is always available.
        $leadId = session("{$this->wizardSessionKey()}.lead_id");
        $lead = null;

        if ($leadId) {
            $lead = \App\Models\Lead::find($leadId);
            if ($lead && !$lead->nikah_profile_id) {
                $lead->update(['nikah_profile_id' => $profile->id]);
            }
        } elseif ($remoteVerification) {
            $lead = \App\Models\Lead::create([
                'name' => $accountData['name'],
                'gender' => $accountData['gender'],
                'phone' => $isEmail ? null : $identifier,
                'email' => $isEmail ? strtolower($identifier) : null,
                'status' => 'registered',
                'assigned_to' => auth()->id(),
                'nikah_profile_id' => $profile->id,
                'source' => 'manual',
                'created_by' => auth()->id(),
            ]);
        }

        $status = "Profile created for {$user->name}.";

        if ($isEmail) {
            Password::sendResetLink(['email' => $user->email]);
            $status .= ' A password-setup link was emailed to them so they can log in.';
        } else {
            $status .= ' No email was provided, so no login link could be sent — add one to their account later via Users, then use "Send Password Reset Link" there.';
        }

        if ($remoteVerification) {
            if ($lead && $lead->phone) {
                if (!$lead->progress_link_token) {
                    $lead->update(['progress_link_token' => Str::random(40)]);
                }
                $link = \App\Http\Controllers\Matchmaker\ClientController::progressLink($lead);
                $status .= " Their CNIC/photo weren't collected today — send them this one secure link and they can upload everything themselves (it also shows their status and proposals): {$link}. They'll enter the last 7 digits of the WhatsApp number on file, every time.";
            } else {
                $status .= ' Their CNIC/photo weren\'t collected today, but a phone number is needed to secure a self-upload link — add one to their account, then generate their progress link from the client page.';
            }
        }

        $fee = $profile->applicableVerificationFee();

        if ($fee > 0 && !empty($paymentData['payment_screenshot'])) {
            $this->recordNikahPaymentSubmission($profile, $paymentData['payment_method'], $paymentData['payment_reference'] ?? null, $paymentData['payment_screenshot']);
            $status .= ' Their payment proof was submitted along with the profile and is now awaiting confirmation.';
        } elseif ($fee > 0) {
            $status .= " A Rs. {$fee} verification fee is due before this profile can be verified — submit their payment proof from this profile's page once collected, or the client can pay it themselves next time they log in.";
        } else {
            $status .= ' No verification fee applies — it can be reviewed and verified as-is.';
        }

        $this->clearWizardSession();

        // A plain matchmaker (nikah.create-profile only, not nikah.view/
        // nikah.manage) can't open the admin verification-review page —
        // that was sending them straight into a 403 right after finishing
        // the wizard. Send them to their own Browse Profiles view of the
        // same profile instead; a full admin still lands on the real
        // review page.
        $redirectRoute = auth()->user()->can('nikah.view') ? 'admin.nikah.show' : 'matchmaker.nikah.show';

        return redirect()->route($redirectRoute, $profile)->with('status', $status);
    }
}
