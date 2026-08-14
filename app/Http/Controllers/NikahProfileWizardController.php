<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasWizardSteps;
use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Models\NikahProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NikahProfileWizardController extends Controller
{
    use HasWizardSteps, ValidatesNikahProfile;

    protected string $wizardKey = 'nikah_profile';

    protected array $wizardStepFields = [
        'basic' => ['age', 'height', 'marital_status', 'education', 'profession', 'city', 'country'],
        'family' => ['caste', 'family_type', 'guardian_name', 'guardian_contact', 'guardian_relation', 'ethnicity', 'language'],
        'deen' => ['sect', 'sect_other', 'prayer_frequency', 'hijab_or_beard', 'smokes', 'diet', 'open_to_polygamy'],
        'about' => ['about', 'expectations', 'pref_min_age', 'pref_max_age', 'pref_city', 'pref_sect', 'pref_education', 'pref_marital_status'],
        'verification' => ['cnic_number', 'cnic_front_image', 'cnic_back_image', 'photo', 'allow_photo_sharing', 'visibility'],
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

        return view("nikah.wizard.step-{$step}", [
            'step' => $step,
            'steps' => $this->wizardSteps(),
            'stepTitles' => $this->stepTitles,
            'data' => $this->wizardStepData($step),
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

        if ($step === 'verification') {
            if (!empty($existing['cnic_front_image'])) {
                $rules['cnic_front_image'] = ['nullable', 'image', 'max:4096'];
            }
            if (!empty($existing['cnic_back_image'])) {
                $rules['cnic_back_image'] = ['nullable', 'image', 'max:4096'];
            }
        }

        $validated = $request->validate($rules, $this->nikahProfileMessages());

        if ($step === 'deen') {
            $validated['sect'] = $this->resolveSect($request);
            $validated['open_to_polygamy'] = $request->boolean('open_to_polygamy');
        }

        if ($step === 'verification') {
            foreach (['cnic_front_image', 'cnic_back_image', 'photo'] as $file) {
                if ($request->hasFile($file)) {
                    $disk = $file === 'photo' ? 'nikah/photos' : 'nikah/cnic';
                    $validated[$file] = $request->file($file)->store($disk, 'private');
                } elseif (!empty($existing[$file])) {
                    $validated[$file] = $existing[$file];
                }
            }

            $validated['allow_photo_sharing'] = $request->boolean('allow_photo_sharing');
        }

        $this->saveWizardStep($step, $validated);

        $steps = $this->wizardSteps();
        $nextIndex = array_search($step, $steps) + 1;

        return $nextIndex < count($steps)
            ? redirect()->route('nikah.create.step', $steps[$nextIndex])
            : redirect()->route('nikah.create.review');
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
        $validated['payment_amount'] = setting('nikah_verification_fee', config('services.nikah.verification_fee'));
        $validated['public_token'] = Str::random(32);

        NikahProfile::create($validated);

        $this->clearWizardSession();
        session()->flash('conversion_event', 'nikah_profile_created');

        return redirect()->route('nikah.payment')->with('status', 'Profile submitted! Please complete the verification fee payment to proceed.');
    }
}
