<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Models\MatchmakerApplication;
use App\Models\User;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

// The public "Become a Nikah Counselor" application (project_matchmaker_
// hiring_document) — guest-friendly like VolunteerController, no account
// required to apply. The 'matchmaker' role itself only gets attached once
// Admin\MatchmakerApplicationController advances an application all the way
// to 'certified' (hiring document's "don't allow instant → dashboard"
// rule) — but the account (and the ability to log in and see a pending-
// approval screen) is created right here, at apply time, so an applicant
// isn't locked out of the app for the whole pipeline's duration.
class NikahCounselorApplicationController extends Controller
{
    use RegistersMinimalUsers;

    public function create()
    {
        return view('nikah-counselor.create');
    }

    // Landing page after a real submission — explains what happens next
    // (the live certification pipeline, read straight from
    // MatchmakerApplication::STEPS so it never goes stale if that pipeline
    // changes), the counselor's actual duties, and a clear Do's/Don'ts list
    // drawn directly from project_matchmaker_hiring_document's conduct
    // rules. No application id/param — an applicant has no account or
    // session to look one up by at this stage anyway, so this is the same
    // static page for everyone who just submitted.
    public function thankYou()
    {
        return view('nikah-counselor.thank-you');
    }

    public function store(Request $request)
    {
        // Honeypot: real visitors never see or fill this field. Still
        // sends them to the real thank-you page — never reveal the trap.
        if ($request->filled('website')) {
            return redirect()->route('nikah-counselor.thank-you');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:30', 'unique:matchmaker_applications,mobile_number'],
            // Required so a certified counselor's account can be matched
            // against (or created with) a real, reachable address — and so
            // certify() can find an existing Sallaamti account by email
            // instead of blindly creating a duplicate one. See certify()'s
            // own comment for the bug this closes.
            'email' => ['required', 'email', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'gender' => ['required', 'in:male,female'],
            'age' => ['required', 'integer', 'min:21', 'max:70'],
            'marital_status' => ['required', 'in:never_married,divorced,widowed,married,separated'],
            'qualification' => ['required', 'in:' . implode(',', array_keys(MatchmakerApplication::QUALIFICATIONS))],
            'qualification_other' => ['required_if:qualification,other', 'nullable', 'string', 'max:100'],
            'selfie_photo' => ['required', 'image', 'max:4096'],
            // CNIC number stays looking mandatory on the form; CNIC front/
            // back photo upload is retired here too, same policy change as
            // the member Nikah profile.
            'cnic_number' => ['nullable', 'string', 'max:20', 'unique:matchmaker_applications,cnic_number'],
            'area' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            // Mandatory, not "add it later" — commission can't be paid out
            // without it, and chasing a certified counselor for these
            // details after the fact is exactly the friction this avoids.
            'payout_method' => ['required', 'in:bank_transfer,jazzcash,easypaisa'],
            'payout_account_title' => ['required', 'string', 'max:255'],
            'payout_account_number' => ['required', 'string', 'max:50'],
            'payout_bank_name' => ['required_if:payout_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'consent_accepted' => ['accepted'],
            'terms_accepted' => ['accepted'],
            // Never applied to an account that already exists under this
            // email — see the user-linking block below. Only ever used to
            // set up a brand-new account's real login password.
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $validated['selfie_photo'] = ImageOptimizer::store($request->file('selfie_photo'), 'matchmaker-applications/selfies', 'private');

        if (Auth::id()) {
            // Already signed in (e.g. an existing member applying from
            // their own account) — that account is the obvious match, and
            // its real password is never touched by this form.
            $validated['user_id'] = Auth::id();
        } else {
            $existingUser = User::where('email', $validated['email'])->first();
            $validated['user_id'] = $existingUser
                ? $existingUser->id
                : $this->createMinimalUser($validated['full_name'], $validated['email'], $validated['mobile_number'], $validated['password'])->id;
        }
        $validated['consent_accepted'] = true;
        $validated['terms_accepted'] = true;
        unset($validated['password']);
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();
        $validated['device_city'] = $this->lookupCity($request->ip());

        MatchmakerApplication::create($validated);

        session()->flash('conversion_event', 'nikah_counselor_applied');

        return redirect()->route('nikah-counselor.thank-you');
    }

    private function lookupCity(?string $ip): ?string
    {
        if (!$ip || in_array($ip, ['127.0.0.1', '::1'])) {
            return null;
        }

        try {
            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}", ['fields' => 'status,city,regionName']);

            if ($response->ok() && $response->json('status') === 'success') {
                return trim(($response->json('city') ?: '') . ', ' . ($response->json('regionName') ?: ''), ', ');
            }
        } catch (\Throwable $e) {
            // Best-effort — never blocks the actual application.
        }

        return null;
    }
}
