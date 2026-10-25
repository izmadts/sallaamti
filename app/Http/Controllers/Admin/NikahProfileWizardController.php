<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Concerns\HasWizardSteps;
use App\Http\Controllers\Concerns\SubmitsNikahPayment;
use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Http\Controllers\Controller;
use App\Models\MatchmakingTimelineEvent;
use App\Models\NikahProfile;
use App\Models\User;
use App\Rules\ValidPhoneNumber;
use App\Services\ImageOptimizer;
use App\Support\CountryStates;
use Illuminate\Http\Request;
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
        'basic' => ['date_of_birth', 'height', 'height_other', 'marital_status', 'has_children', 'children_count', 'living_situation', 'education', 'profession', 'city', 'address', 'state', 'country'],
        'family' => ['caste', 'family_type', 'guardian_name', 'guardian_contact', 'guardian_relation', 'ethnicity', 'languages', 'language_other'],
        'deen' => ['sect', 'sect_other', 'prayer_frequency', 'hijab_or_beard', 'smokes', 'diet', 'open_to_polygamy'],
        'about' => ['about', 'expectations', 'visibility', 'pref_min_age', 'pref_max_age', 'pref_city', 'pref_sect', 'pref_education', 'pref_marital_status', 'pref_income', 'pref_other'],
        // Optional — a matchmaker collecting payment on the spot can submit
        // it right here; skippable if the client isn't paying today (they
        // can pay later themselves, or it can be submitted afterward from
        // the profile page — see Matchmaker\NikahBrowseController::
        // submitPayment()).
        'payment' => ['payment_method', 'payment_reference', 'payment_screenshot'],
    ];

    // Genuinely required to create a real profile — checked at finalize()
    // regardless of how each step was reached, so an admin who used
    // skipStep() to preview the wizard can never accidentally create a
    // real (broken) User/NikahProfile/Lead by finalizing anyway.
    // guardian_contact and expectations are deliberately NOT in this list —
    // see saveStep()'s own comment on why they're relaxed to optional for a
    // walk-in/phone intake, unlike the member's own self-service wizard.
    private const GENUINELY_REQUIRED = ['identifier', 'name', 'gender', 'date_of_birth', 'height', 'profession', 'city', 'state', 'country', 'education', 'sect', 'guardian_name'];

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

    // Entry point from the "Nikah" link on an existing user's row in Admin
    // > Users — for exactly the case that used to dead-end at "The
    // identifier has already been taken" on the account step: someone who
    // already has a Sallaamti login (self-registered, Google, whatever) but
    // never finished their own Nikah profile, and a counselor now wants to
    // do it for them. Pre-fills the account step from the real account
    // instead of asking for name/email/phone again, and finalize() below
    // attaches the profile to this same User rather than creating a new
    // one.
    public function startForUser(User $user)
    {
        $this->authorizeProfileCreation();

        if ($user->nikahProfile) {
            return redirect()->route('admin.nikah.show', $user->nikahProfile)
                ->with('status', "{$user->name} already has a Nikah profile.");
        }

        $this->clearWizardSession();

        session(["{$this->wizardSessionKey()}.existing_user_id" => $user->id]);

        $this->saveWizardStep('account', [
            'name' => $user->name,
            'identifier' => $user->email ?: $user->phone,
            'gender' => $user->gender,
        ]);

        // Gender isn't always on file for an older/social-login account —
        // GENUINELY_REQUIRED needs it, so leave the account step open for
        // the counselor to pick it rather than letting a blank value ride
        // through as "completed".
        if (blank($user->gender)) {
            session([
                "{$this->wizardSessionKey()}.completed" => array_values(
                    array_diff($this->wizardCompletedSteps(), ['account'])
                ),
            ]);
        }

        return redirect()->route("{$this->routeNamePrefix()}.nikah.profiles.create");
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
            'expectedFee' => $step === 'payment' ? NikahProfile::feeForMaritalStatus($this->wizardStepData('basic')['marital_status'] ?? null, $this->wizardStepData('account')['gender'] ?? null, $this->wizardStepData('basic')['city'] ?? null) : null,
            'existingUser' => $step === 'account'
                ? User::find(session("{$this->wizardSessionKey()}.existing_user_id"))
                : null,
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

            // Linking to an existing user (see startForUser()) re-submits
            // that same user's own email/phone through this step — ignore
            // their own row in the uniqueness check, or it would reject it
            // as already taken by... themselves.
            $existingUserId = session("{$this->wizardSessionKey()}.existing_user_id");

            Validator::make(['identifier' => $identifier], [
                'identifier' => [
                    $isEmail ? 'email' : new ValidPhoneNumber(),
                    Rule::unique(User::class, $isEmail ? 'email' : 'phone')->ignore($existingUserId),
                ],
            ], [
                'identifier.unique' => __('db.This email or phone number is already registered to another account.'),
            ])->validate();

            $validated['identifier'] = $identifier;
            $this->saveWizardStep('account', $validated);

            return $this->nextStepRedirect('account');
        }

        $rules = collect($this->nikahProfileRules())->only($this->wizardStepFields[$step])->toArray();

        // Required for a client filling in their own profile (the shared
        // ValidatesNikahProfile rule, used as-is by the member wizard) —
        // but a counselor taking down a rushed phone-call/walk-in intake
        // may not have the guardian's contact number or a reflective
        // "what are you looking for" answer on hand yet. Relaxed to
        // optional here only; the client fills them in themselves later
        // (self-service edit already requires them again at that point),
        // or a counselor adds them afterward via the profile's Edit action.
        if (isset($rules['guardian_contact'])) {
            $rules['guardian_contact'] = ['nullable', 'string', 'max:20'];
        }
        if (isset($rules['expectations'])) {
            $rules['expectations'] = ['nullable', 'string', 'max:2000'];
        }

        $validated = $request->validate($rules, $this->nikahProfileMessages());

        if ($step === 'basic') {
            $validated['height'] = $this->resolveHeight($request);
        }

        if ($step === 'family') {
            $validated['language'] = $this->resolveLanguage($request);
        }

        if ($step === 'deen') {
            $validated['sect'] = $this->resolveSect($request);
            $validated['open_to_polygamy'] = $request->boolean('open_to_polygamy');
        }

        $this->saveWizardStep($step, $validated);

        return $this->nextStepRedirect($step, $request);
    }

    /**
     * Lets a real admin (not just anyone with nikah.create-profile —
     * matchmakers register real walk-in clients through this same wizard,
     * and must never have their step validation quietly bypassed) click
     * through every step, including all the way to the review screen, to
     * see what each one looks like — without needing to type real data
     * into required fields first. Marks the step "completed" with
     * whatever was already there (blank, if never visited) purely so
     * HasWizardSteps' own completeness check lets admin continue past it;
     * it never creates anything itself, and finalize() separately refuses
     * to create a real profile/user/Lead unless GENUINELY_REQUIRED
     * actually got filled in on some pass through the wizard, so a
     * preview run can't leave broken data behind even if admin clicks all
     * the way to "Finalize" afterward.
     */
    public function skipStep(string $step)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);
        abort_unless(array_key_exists($step, $this->wizardStepFields), 404);

        $this->saveWizardStep($step, $this->wizardStepData($step));

        return $this->nextStepRedirect($step);
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

        // A step reached via the admin-only skipStep() preview leaves its
        // fields blank — catch that here rather than let it fall through
        // to createMinimalUser()/NikahProfile::create() with missing data,
        // which would otherwise create a real, broken record.
        $missing = collect(self::GENUINELY_REQUIRED)->filter(fn ($field) => blank($data[$field] ?? null));
        if ($missing->isNotEmpty()) {
            // Straight to the first step, not through start() — every step
            // already reads as "completed" at this point (that's the only
            // way finalize() was reached at all), so there's no single
            // "the" incomplete step to single out; and going through
            // start()'s own redirect would lose this flashed message along
            // the way (flash data only survives one redirect hop).
            return redirect()->route("{$this->routeNamePrefix()}.nikah.profiles.create.step", $this->wizardSteps()[0])
                ->with('error', 'Some required fields are still empty (' . $missing->implode(', ') . ') — looks like one or more steps were skipped rather than filled in. Go back and complete them before finalizing.');
        }

        $accountData = collect($data)->only(['name', 'identifier', 'gender'])->toArray();
        $paymentData = collect($data)->only(['payment_method', 'payment_reference', 'payment_screenshot'])->toArray();
        // Payment fields are handled separately below via
        // recordNikahPaymentSubmission() (sets payment_status/amount
        // together, not left to mass-assignment) — excluded here so
        // create() doesn't half-apply them without a status.
        $profileData = collect($data)->except(['name', 'identifier', 'gender', 'payment_method', 'payment_reference', 'payment_screenshot'])->toArray();

        $identifier = $accountData['identifier'];
        $isEmail = (bool) filter_var($identifier, FILTER_VALIDATE_EMAIL);

        $existingUser = ($existingUserId = session("{$this->wizardSessionKey()}.existing_user_id"))
            ? User::find($existingUserId)
            : null;

        // A brand-new account gets a real, visible temp password right away
        // (not a random unknown one + a separate manual "set password"
        // step, and not an emailed reset link either) — the whole point is
        // the counselor has something to copy-paste to the client the
        // moment this page loads. An existing (startForUser()-linked)
        // account already has a working password — never touch it.
        $plainPassword = null;

        if ($existingUser) {
            $user = $existingUser;
            $user->update(['name' => $accountData['name'], 'gender' => $accountData['gender'], 'city' => $profileData['city'] ?? $user->city]);
        } else {
            $plainPassword = $this->generateTempPassword();
            $user = $this->createMinimalUser(
                $accountData['name'],
                $isEmail ? strtolower($identifier) : null,
                $isEmail ? null : $identifier,
                $plainPassword,
                'password',
                true,
            );
            $user->update(['gender' => $accountData['gender'], 'city' => $profileData['city'] ?? null]);
        }

        $profileData['user_id'] = $user->id;
        $profileData['created_by'] = auth()->id();
        $profileData['allow_photo_sharing'] = (bool) ($profileData['allow_photo_sharing'] ?? false);
        $profileData['open_to_polygamy'] = (bool) ($profileData['open_to_polygamy'] ?? false);
        $profileData['payment_amount'] = NikahProfile::feeForMaritalStatus($profileData['marital_status'] ?? null, $accountData['gender'] ?? null, $profileData['city'] ?? null);
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

        if ($existingUser) {
            // They already have a working login (self-registered, Google,
            // whatever) — no new password needed, unlike the brand-new-
            // account branch below.
            $status .= ' Linked to their existing Sallaamti account — they can log in as usual, no new password was created.';
        } else {
            $status .= ' A temporary login password was generated — see below to copy and send it.';

            MatchmakingTimelineEvent::log($lead, $profile, 'credentials_issued', 'Login credentials generated and shown to the Nikah Counselor for hand-off.');
        }

        // CNIC/photo are never collected in this flow (see $wizardStepFields'
        // comment) — have them download the app and finish those details
        // themselves. The old self-service /p/{lead} link still exists
        // (regenerate it from the client page if you ever need it for
        // consent/proposal/package-payment self-service), it's just no
        // longer pushed by default here — a normal app/website login is
        // simpler to explain than a token link + WhatsApp re-verification.
        $status .= ' Their CNIC/photo weren\'t collected today — have them download the Sallaamti app (or use the website) and log in with their phone/email and the password above to fill in those and any other remaining details themselves.';

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

        $redirect = redirect()->route($redirectRoute, $lead)->with('status', $status);

        // One-time-read flash (gone on the next request) — the plain
        // password is never persisted anywhere past this single page load;
        // the counselor copies it from here or, if they miss it, falls
        // back to the existing "Client Login Password" manual reset.
        if ($plainPassword) {
            $redirect->with('new_client_credentials', [
                'identifier' => $identifier,
                'password' => $plainPassword,
                'is_email' => $isEmail,
                'app_url' => setting('mobile_app_android_url', 'https://play.google.com/store/apps/details?id=com.sallaamti.app&pcampaignid=web_share'),
            ]);
        }

        return $redirect;
    }
}
