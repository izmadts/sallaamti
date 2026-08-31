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
// account yet). CNIC/photo are deliberately NOT collected here at all —
// see the comment on $wizardStepFields below.
class NikahProfileWizardController extends Controller
{
    use HasWizardSteps, ValidatesNikahProfile, RegistersMinimalUsers, SubmitsNikahPayment;

    protected string $wizardKey = 'admin_nikah_profile';

    // CNIC/photo were deliberately dropped from this walk-in flow entirely
    // (project decision, 2026-08-31) — a counselor collecting a stranger's
    // ID document/photo over the phone or on the spot was exactly the "no
    // CNIC over WhatsApp" problem the old remote_verification checkbox
    // existed to work around case-by-case. Rather than a per-registration
    // choice, self-upload is now just how it always works: the counselor
    // registers everything else and takes payment, and the client uploads
    // their own CNIC/photo later from their own progress link after
    // logging in (see finalize()'s always-on progress-link status message,
    // and Matchmaker\ClientController::setLoginPassword()/web equivalent
    // for how they get a real login to do that with).
    protected array $wizardStepFields = [
        'account' => ['name', 'identifier', 'gender'],
        'basic' => ['date_of_birth', 'height', 'height_other', 'marital_status', 'has_children', 'children_count', 'living_situation', 'education', 'profession', 'city', 'state', 'country'],
        'family' => ['caste', 'family_type', 'guardian_name', 'guardian_contact', 'guardian_relation', 'ethnicity'],
        'deen' => ['sect', 'sect_other', 'prayer_frequency', 'hijab_or_beard', 'smokes', 'diet', 'open_to_polygamy'],
        'about' => ['about', 'expectations', 'visibility'],
        // Optional — a matchmaker collecting payment on the spot can submit
        // it right here; skippable if the client isn't paying today (they
        // can pay later themselves, or it can be submitted afterward from
        // the profile page — see Matchmaker\NikahBrowseController::
        // submitPayment()).
        'payment' => ['payment_method', 'payment_reference', 'payment_screenshot'],
    ];

    protected array $stepTitles = [
        'account' => 'Login Account',
        'basic' => 'Basic Info',
        'family' => 'Family & Guardian',
        'deen' => 'Deen & Lifestyle',
        'about' => 'About',
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

        $validated = $request->validate($rules, $this->nikahProfileMessages());

        if ($step === 'basic') {
            $validated['height'] = $this->resolveHeight($request);
        }

        if ($step === 'deen') {
            $validated['sect'] = $this->resolveSect($request);
            $validated['open_to_polygamy'] = $request->boolean('open_to_polygamy');
        }

        $this->saveWizardStep($step, $validated);

        return $this->nextStepRedirect($step, $request);
    }

    // Accepts the request so a step whose view submits via fetch() with
    // AJAX headers gets a JSON redirect instead of a real 302 (useful for
    // turning a browser-level upload failure, e.g. Chrome's
    // ERR_UPLOAD_FILE_CHANGED, into a friendly in-place error instead of
    // Chrome's network-error screen) — not currently used by any step in
    // this wizard, but kept since a future file-upload step may want it.
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
        // Payment fields are handled separately below via
        // recordNikahPaymentSubmission() (sets payment_status/amount
        // together, not left to mass-assignment) — excluded here so
        // create() doesn't half-apply them without a status.
        $profileData = collect($data)->except(['name', 'identifier', 'gender', 'payment_method', 'payment_reference', 'payment_screenshot'])->toArray();

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

        // Link (or create) the Lead this profile belongs to — needed so the
        // client's own self-upload of their CNIC/photo (always required
        // now — see $wizardStepFields' comment) has a WhatsApp number and a
        // progress link to be gated by. If the wizard was reached via
        // ClientController::convert(), that Lead is reused (and, as a side
        // fix, actually gets linked back now — previously the matchmaker
        // had to do that manually afterward). Otherwise a minimal Lead is
        // always created so the "one link for everything" mechanism is
        // always available.
        $leadId = session("{$this->wizardSessionKey()}.lead_id");
        $lead = null;

        if ($leadId) {
            $lead = \App\Models\Lead::find($leadId);
            if ($lead && !$lead->nikah_profile_id) {
                $lead->update(['nikah_profile_id' => $profile->id]);
            }
        } else {
            // Always create a Lead for a walk-in registration too — not
            // only when remote verification was requested. Without this, a
            // profile registered in person (the common case) never gets a
            // Lead, so it never shows up in the counselor's My Clients
            // workspace (stage tracker, follow-ups, consent, proposals) —
            // it only existed as a bare NikahProfile they'd have to find
            // via Browse instead. Lead is meant to be the one model every
            // client routes through (see ClientController's class comment,
            // spec doc §52), so this brings walk-ins in line with that.
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
            $status .= ' A password-setup link was emailed to them so they can log in — or set a temporary password yourself below and hand it to them directly.';
        } else {
            $status .= ' Set a temporary login password for them below (under "Client Login Password") and hand it to them directly — they\'ll be asked to choose their own once they log in.';
        }

        // CNIC/photo are never collected in this flow (see $wizardStepFields'
        // comment) — the client always needs their own link to upload those
        // later, not only in some conditional case.
        if ($lead && $lead->phone) {
            if (!$lead->progress_link_token) {
                $lead->update(['progress_link_token' => Str::random(40)]);
            }
            $link = \App\Http\Controllers\Matchmaker\ClientController::progressLink($lead);
            $status .= " Their CNIC/photo weren't collected today — send them this one secure link and they can upload everything themselves (it also shows their status and proposals): {$link}. They'll enter the last 7 digits of the WhatsApp number on file, every time.";
        } else {
            $status .= ' Their CNIC/photo weren\'t collected today, but a phone number is needed to secure a self-upload link — add one to their account, then generate their progress link from the client page.';
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

        // Land back on the Lead's own page (not the Nikah profile's) —
        // that's where "Client Login Password" and the progress-link
        // controls actually live, so the counselor can immediately set
        // credentials and hand off the self-upload link right after
        // finishing, instead of having to navigate there separately.
        // routeNamePrefix() (not the nikah.view permission check this used
        // before) is the right switch here since this is about which Lead
        // page the current user's role can actually reach, not about
        // Nikah-profile-specific permissions.
        $redirectRoute = $this->routeNamePrefix() === 'admin' ? 'admin.leads.show' : 'matchmaker.clients.show';

        return redirect()->route($redirectRoute, $lead)->with('status', $status);
    }
}
