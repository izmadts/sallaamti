<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MatchmakerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

// The applicant reviews and actually accepts the Nikah Counselor Agreement
// + NDA themselves — no account needed yet (certification hasn't happened),
// so this uses the same signed-link + last-7-digits-of-mobile pattern as
// Lead's progress link, rather than requiring a login that doesn't exist
// yet. Admin can still see/advance the pipeline manually, but this is the
// real acceptance event — the pipeline dropdown alone no longer implies it
// happened. IMPORTANT: the agreement text in the accompanying view is a
// draft built from project_matchmaker_hiring_document's own checklist —
// it is NOT a substitute for review by a Pakistani lawyer before relying
// on it, exactly as that document itself says.
class MatchmakerAgreementController extends Controller
{
    public function show(Request $request, MatchmakerApplication $matchmakerApplication)
    {
        $this->assertValidToken($request, $matchmakerApplication);

        $verifyUrl = URL::signedRoute('public.matchmaker-agreement.verify', ['matchmakerApplication' => $matchmakerApplication->id, 'token' => $matchmakerApplication->agreement_link_token]);

        return view('public.matchmaker-agreement.show', ['application' => $matchmakerApplication, 'verifyUrl' => $verifyUrl, 'unlocked' => false]);
    }

    public function verify(Request $request, MatchmakerApplication $matchmakerApplication)
    {
        $this->assertValidToken($request, $matchmakerApplication);

        $validated = $request->validate(['last7' => ['required', 'digits:7']]);
        $expected = substr(preg_replace('/\D/', '', $matchmakerApplication->mobile_number ?? ''), -7);
        $verifyUrl = URL::signedRoute('public.matchmaker-agreement.verify', ['matchmakerApplication' => $matchmakerApplication->id, 'token' => $matchmakerApplication->agreement_link_token]);

        if (!$expected || $validated['last7'] !== $expected) {
            return view('public.matchmaker-agreement.show', ['application' => $matchmakerApplication, 'verifyUrl' => $verifyUrl, 'unlocked' => false])
                ->with('error', 'That doesn\'t match — check the last 7 digits of the mobile number on your application and try again.');
        }

        $acceptUrl = URL::signedRoute('public.matchmaker-agreement.accept', ['matchmakerApplication' => $matchmakerApplication->id, 'token' => $matchmakerApplication->agreement_link_token]);

        return view('public.matchmaker-agreement.show', ['application' => $matchmakerApplication, 'verifyUrl' => $verifyUrl, 'acceptUrl' => $acceptUrl, 'unlocked' => true, 'last7' => $validated['last7']]);
    }

    public function accept(Request $request, MatchmakerApplication $matchmakerApplication)
    {
        $this->assertValidToken($request, $matchmakerApplication);

        $last7 = (string) $request->input('last7');
        $expected = substr(preg_replace('/\D/', '', $matchmakerApplication->mobile_number ?? ''), -7);
        $verifyUrl = URL::signedRoute('public.matchmaker-agreement.verify', ['matchmakerApplication' => $matchmakerApplication->id, 'token' => $matchmakerApplication->agreement_link_token]);

        if (!$expected || $last7 !== $expected) {
            return view('public.matchmaker-agreement.show', ['application' => $matchmakerApplication, 'verifyUrl' => $verifyUrl, 'unlocked' => false])
                ->with('error', 'Your verification expired — please enter the last 7 digits again.');
        }

        $validated = $request->validate([
            'agreement_accepted' => ['accepted'],
            'nda_accepted' => ['accepted'],
        ]);

        $matchmakerApplication->update([
            'agreement_accepted_at' => now(),
            'agreement_ip' => $request->ip(),
            'nda_accepted_at' => now(),
            'nda_ip' => $request->ip(),
        ]);

        // Reflects reality in the pipeline without removing admin's own
        // manual control over every other stage — only bumps forward if
        // they haven't already been moved past this point some other way.
        $ndaIndex = array_search('nda_signed', array_keys(MatchmakerApplication::STEPS), true);
        $currentIndex = array_search($matchmakerApplication->status, array_keys(MatchmakerApplication::STEPS), true);
        if ($currentIndex !== false && $currentIndex < $ndaIndex) {
            $matchmakerApplication->update(['status' => 'nda_signed']);
        }

        return view('public.matchmaker-agreement.accepted', ['application' => $matchmakerApplication]);
    }

    private function assertValidToken(Request $request, MatchmakerApplication $matchmakerApplication): void
    {
        abort_unless($matchmakerApplication->agreement_link_token && $request->query('token') === $matchmakerApplication->agreement_link_token, 403, 'This link is no longer valid — ask Sallaamti for a fresh one.');
    }
}
