<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\MatchmakingLinkAccess;
use App\Models\MatchmakingTimelineEvent;
use App\Models\User;
use App\Notifications\NewNikahVerificationDocumentsSubmitted;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

// A standing page a client can revisit any time to see their own status,
// timeline, and full proposal/response history — no login, but re-verified
// on every single visit against the last 7 digits of the WhatsApp number on
// file, rather than a one-time check that then persists in a session. That's
// deliberate: the link itself doesn't expire and may sit in a chat thread
// indefinitely, so nothing about "already unlocked" should survive between
// visits. See Matchmaker\ClientController::regenerateProgressLink().
//
// This is deliberately the ONE link a matchmaker ever sends a client — see
// project_matchmaker_hiring_document's "no CNIC over WhatsApp" rule and the
// user's explicit "don't disturb the customer with multiple links" steer.
// When a profile was registered remotely without CNIC/photo, this same page
// also carries the self-upload form (uploadDocuments() below) rather than
// minting a second, separate link for that.
class MatchmakingProgressController extends Controller
{
    use ValidatesNikahProfile;

    public function show(Request $request, Lead $lead)
    {
        $this->assertValidToken($request, $lead);

        MatchmakingLinkAccess::record($lead, 'progress_view', $request);

        $verifyUrl = URL::signedRoute('public.matchmaking.progress.verify', ['lead' => $lead->id, 'token' => $lead->progress_link_token]);

        return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'unlocked' => false]);
    }

    public function verify(Request $request, Lead $lead)
    {
        $this->assertValidToken($request, $lead);

        $validated = $request->validate([
            'last7' => ['required', 'digits:7'],
        ]);

        $expected = substr(preg_replace('/\D/', '', $lead->phone ?? ''), -7);
        $verifyUrl = URL::signedRoute('public.matchmaking.progress.verify', ['lead' => $lead->id, 'token' => $lead->progress_link_token]);

        if (!$expected || $validated['last7'] !== $expected) {
            MatchmakingLinkAccess::record($lead, 'progress_verify', $request, 'failed');

            return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'unlocked' => false])
                ->with('error', 'That doesn\'t match — check the last 7 digits of the WhatsApp number your matchmaker has on file and try again.');
        }

        MatchmakingLinkAccess::record($lead, 'progress_verify', $request, 'success');

        $lead->load(['timelineEvents.matchmaker', 'proposalBatches.proposals.candidate.user', 'nikahProfile']);

        $documentsUrl = URL::signedRoute('public.matchmaking.progress.documents', ['lead' => $lead->id, 'token' => $lead->progress_link_token]);

        return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'documentsUrl' => $documentsUrl, 'unlocked' => true, 'last7' => $validated['last7']]);
    }

    // Re-checks the same last-7-digits proof (carried forward as a hidden
    // field from the verify() form, not re-typed) before accepting any
    // upload — nothing about "already unlocked" persists server-side
    // between requests, matching verify()'s own stated design.
    public function uploadDocuments(Request $request, Lead $lead)
    {
        $this->assertValidToken($request, $lead);

        $last7 = (string) $request->input('last7');
        $expected = substr(preg_replace('/\D/', '', $lead->phone ?? ''), -7);
        $verifyUrl = URL::signedRoute('public.matchmaking.progress.verify', ['lead' => $lead->id, 'token' => $lead->progress_link_token]);

        if (!$expected || $last7 !== $expected) {
            MatchmakingLinkAccess::record($lead, 'documents_upload', $request, 'failed');

            return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'unlocked' => false])
                ->with('error', 'Your verification expired — please enter the last 7 digits again.');
        }

        abort_unless($lead->nikahProfile, 404);
        $profile = $lead->nikahProfile;

        $documentsUrl = URL::signedRoute('public.matchmaking.progress.documents', ['lead' => $lead->id, 'token' => $lead->progress_link_token]);

        $rules = collect($this->nikahProfileRules($profile))
            ->only(['cnic_number', 'cnic_front_image', 'cnic_back_image', 'photo', 'allow_photo_sharing'])
            ->toArray();

        try {
            $documentData = $request->validate($rules, $this->nikahProfileMessages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $lead->load(['timelineEvents.matchmaker', 'proposalBatches.proposals.candidate.user', 'nikahProfile']);

            return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'documentsUrl' => $documentsUrl, 'unlocked' => true, 'last7' => $last7])
                ->withErrors($e->errors());
        }

        foreach (['cnic_front_image', 'cnic_back_image', 'photo'] as $file) {
            if ($request->hasFile($file)) {
                $disk = $file === 'photo' ? 'nikah/photos' : 'nikah/cnic';
                $maxDimension = $file === 'photo' ? 1200 : 1600;
                $quality = $file === 'photo' ? 82 : 85;
                $documentData[$file] = ImageOptimizer::store($request->file($file), $disk, 'private', maxDimension: $maxDimension, quality: $quality);
            } else {
                unset($documentData[$file]);
            }
        }

        $documentData['allow_photo_sharing'] = $request->boolean('allow_photo_sharing');
        $profile->update($documentData);

        MatchmakingLinkAccess::record($lead, 'documents_upload', $request, 'success');
        MatchmakingTimelineEvent::log($lead, $profile, 'documents_uploaded', 'Client uploaded their CNIC/photo themselves via their secure progress link.');

        User::role('admin')->each(function ($admin) use ($profile) {
            try {
                $admin->notify(new NewNikahVerificationDocumentsSubmitted($profile));
            } catch (\Throwable $e) {
                \Log::error('NewNikahVerificationDocumentsSubmitted notification failed: ' . $e->getMessage());
            }
        });

        $lead->load(['timelineEvents.matchmaker', 'proposalBatches.proposals.candidate.user', 'nikahProfile']);

        return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'documentsUrl' => $documentsUrl, 'unlocked' => true])
            ->with('status', 'Thank you — your documents have been submitted for verification.');
    }

    private function assertValidToken(Request $request, Lead $lead): void
    {
        abort_unless($lead->progress_link_token && $request->query('token') === $lead->progress_link_token, 403, 'This link is no longer valid — ask your matchmaker for a fresh one.');
    }
}
