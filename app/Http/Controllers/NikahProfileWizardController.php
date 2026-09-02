<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasWizardSteps;
use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Models\NikahProfile;
use App\Services\ImageOptimizer;
use App\Support\CountryStates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NikahProfileWizardController extends Controller
{
    use HasWizardSteps, ValidatesNikahProfile;

    protected string $wizardKey = 'nikah_profile';

    protected array $wizardStepFields = [
        'basic' => ['date_of_birth', 'height', 'height_other', 'marital_status', 'has_children', 'children_count', 'living_situation', 'education', 'profession', 'city', 'state', 'country'],
        'family' => ['caste', 'family_type', 'guardian_name', 'guardian_contact', 'guardian_relation', 'ethnicity', 'languages', 'languages.*', 'language_other'],
        'deen' => ['sect', 'sect_other', 'prayer_frequency', 'hijab_or_beard', 'smokes', 'diet', 'open_to_polygamy'],
        'about' => ['about', 'expectations', 'pref_min_age', 'pref_max_age', 'pref_city', 'pref_sect', 'pref_education', 'pref_marital_status'],
        'verification' => ['cnic_number', 'photo', 'allow_photo_sharing', 'visibility'],
    ];

    protected array $stepTitles = [
        'basic' => 'Basic Info',
        'family' => 'Family & Background',
        'deen' => 'Deen & Lifestyle',
        'about' => 'About & Preferences',
        'verification' => 'Photos & Verification',
    ];

    public function start()
    {
        if (Auth::user()->nikahProfile) {
            return redirect()->route('nikah.show');
        }

        $step = $this->firstIncompleteWizardStep();

        return $step
            ? redirect()->route('nikah.create.step', $step)
            : redirect()->route('nikah.create.review');
    }

    public function showStep(string $step)
    {
        if (Auth::user()->nikahProfile) {
            return redirect()->route('nikah.show');
        }

        abort_unless(array_key_exists($step, $this->wizardStepFields), 404);

        $data = $this->wizardStepData($step);

        // Don't make members retype what they already told us on their main
        // account profile — prefill from there the first time this step is
        // shown, but never clobber anything they've already entered here.
        if ($step === 'basic' && empty($data['city']) && Auth::user()->city) {
            $data['city'] = Auth::user()->city;
        }

        return view("nikah.wizard.step-{$step}", [
            'step' => $step,
            'steps' => $this->wizardSteps(),
            'stepTitles' => $this->stepTitles,
            'data' => $data,
            'accountGender' => Auth::user()->gender,
            'countries' => $step === 'basic' ? CountryStates::countries() : [],
            'countryStates' => $step === 'basic' ? CountryStates::map() : [],
        ]);
    }

    public function saveStep(Request $request, string $step)
    {
        abort_unless(array_key_exists($step, $this->wizardStepFields), 404);

        if (Auth::user()->nikahProfile) {
            return redirect()->route('nikah.show');
        }

        $rules = collect($this->nikahProfileRules())->only($this->wizardStepFields[$step])->toArray();
        $existing = $this->wizardStepData($step);

        // Gender isn't a NikahProfile column — it lives on the account and
        // is never duplicated — but matching is opposite-gender, so it must
        // be set before a profile can be created. Collect it here instead
        // of bouncing the member to a separate page.
        if ($step === 'basic') {
            $rules['gender'] = ['required', 'in:male,female'];
        }

        $validated = $request->validate($rules, $this->nikahProfileMessages());

        if ($step === 'basic') {
            if ($validated['gender'] !== Auth::user()->gender) {
                Auth::user()->update(['gender' => $validated['gender']]);
            }
            unset($validated['gender']);
            $validated['height'] = $this->resolveHeight($request);
        }

        if ($step === 'family') {
            $validated['language'] = $this->resolveLanguage($request);
        }

        if ($step === 'deen') {
            $validated['sect'] = $this->resolveSect($request);
            $validated['open_to_polygamy'] = $request->boolean('open_to_polygamy');
        }

        if ($step === 'verification') {
            // CNIC front/back upload retired — only the profile photo is
            // stored as a file here now.
            try {
                if ($request->hasFile('photo')) {
                    $validated['photo'] = ImageOptimizer::store($request->file('photo'), 'nikah/photos', 'private', maxDimension: 1200, quality: 82);
                } elseif (!empty($existing['photo'])) {
                    $validated['photo'] = $existing['photo'];
                }
            } catch (\Throwable $e) {
                report($e);
                return back()->withInput()->withErrors(['photo' => 'Sorry, we could not save your uploaded photo — please try again in a moment. If this keeps happening, contact support.']);
            }

            $validated['allow_photo_sharing'] = $request->boolean('allow_photo_sharing');
        }

        $this->saveWizardStep($step, $validated);

        $steps = $this->wizardSteps();
        $nextIndex = array_search($step, $steps) + 1;

        $redirectUrl = $nextIndex < count($steps)
            ? route('nikah.create.step', $steps[$nextIndex])
            : route('nikah.create.review');

        // The verification step submits via fetch() (see step-verification's
        // script) specifically so a browser-level upload failure — e.g.
        // Chrome's ERR_UPLOAD_FILE_CHANGED, thrown when a selected file was
        // touched by something like OneDrive sync between selection and
        // submit — surfaces as a friendly in-page message instead of
        // replacing the whole page with Chrome's own network-error screen.
        // A JSON response is what makes that possible; the plain redirect
        // stays the fallback for non-JS/JS-disabled submissions.
        if ($request->wantsJson()) {
            return response()->json(['redirect' => $redirectUrl]);
        }

        return redirect($redirectUrl);
    }

    public function review()
    {
        if (Auth::user()->nikahProfile) {
            return redirect()->route('nikah.show');
        }

        if ($incomplete = $this->firstIncompleteWizardStep()) {
            return redirect()->route('nikah.create.step', $incomplete);
        }

        return view('nikah.wizard.review', [
            'steps' => $this->wizardSteps(),
            'stepTitles' => $this->stepTitles,
            'data' => $this->wizardAllData(),
        ]);
    }

    public function finalize()
    {
        if (Auth::user()->nikahProfile) {
            return redirect()->route('nikah.show');
        }

        if ($this->firstIncompleteWizardStep()) {
            return redirect()->route('nikah.create');
        }

        $validated = $this->wizardAllData();
        $validated['user_id'] = Auth::id();
        $validated['payment_amount'] = NikahProfile::feeForMaritalStatus($validated['marital_status'] ?? null);
        $validated['public_token'] = Str::random(32);

        NikahProfile::create($validated);

        // Keep the two profiles in sync both ways — if the account-level
        // city was never set, this is real data the member just typed, so
        // carry it over instead of leaving it blank.
        if (!Auth::user()->city && !empty($validated['city'])) {
            Auth::user()->update(['city' => $validated['city']]);
        }

        $this->clearWizardSession();
        session()->flash('conversion_event', 'nikah_profile_created');

        return redirect()->route('nikah.payment')->with('status', 'Profile submitted! Please complete the verification fee payment to proceed.');
    }
}
