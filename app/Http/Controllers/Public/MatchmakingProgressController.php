<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\MatchmakingConsent;
use App\Models\MatchmakingConsentRequest;
use App\Models\MatchmakingLinkAccess;
use App\Models\MatchmakingTimelineEvent;
use App\Models\User;
use App\Notifications\MatchmakerConsentResponded;
use App\Notifications\NewNikahVerificationDocumentsSubmitted;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
// A remotely-registered profile's document upload (uploadDocuments()) and
// any pending consent confirmations (respondToConsent()) both live on this
// same page rather than minting separate links for each.
class MatchmakingProgressController extends Controller
{
    use ValidatesNikahProfile;

    public function show(Request $request, Lead $lead)
    {
        $this->assertValidToken($request, $lead);

        MatchmakingLinkAccess::record($lead, 'progress_view', $request);

        $verifyUrl = route('public.matchmaking.progress.verify', ['lead' => $lead->id, 't' => $lead->progress_link_token]);

        return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'unlocked' => false]);
    }

    public function verify(Request $request, Lead $lead)
    {
        $this->assertValidToken($request, $lead);

        $validated = $request->validate([
            'last7' => ['required', 'digits:7'],
        ]);

        $expected = substr(preg_replace('/\D/', '', $lead->phone ?? ''), -7);

        if (!$expected || $validated['last7'] !== $expected) {
            MatchmakingLinkAccess::record($lead, 'progress_verify', $request, 'failed');

            $verifyUrl = route('public.matchmaking.progress.verify', ['lead' => $lead->id, 't' => $lead->progress_link_token]);

            return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'unlocked' => false])
                ->with('error', 'That doesn\'t match — check the last 7 digits of the WhatsApp number your matchmaker has on file and try again.')
                ->with('error_ur', 'یہ نمبر میل نہیں کھاتا — اپنے میچ میکر کے پاس موجود واٹس ایپ نمبر کے آخری 7 ہندسے چیک کر کے دوبارہ کوشش کریں۔');
        }

        MatchmakingLinkAccess::record($lead, 'progress_verify', $request, 'success');

        return $this->unlockedView($lead, $validated['last7']);
    }

    // Re-checks the same last-7-digits proof (carried forward as a hidden
    // field from the verify() form, not re-typed) before accepting any
    // upload — nothing about "already unlocked" persists server-side
    // between requests, matching verify()'s own stated design.
    public function uploadDocuments(Request $request, Lead $lead)
    {
        $this->assertValidToken($request, $lead);

        if (!$this->reverifyLast7($lead, $request)) {
            MatchmakingLinkAccess::record($lead, 'documents_upload', $request, 'failed');

            return $this->lockedView($lead, 'Your verification expired — please enter the last 7 digits again.', 'آپ کی تصدیق ختم ہو گئی — براہ کرم آخری 7 ہندسے دوبارہ درج کریں۔');
        }

        $last7 = (string) $request->input('last7');
        abort_unless($lead->nikahProfile, 404);
        $profile = $lead->nikahProfile;

        $rules = collect($this->nikahProfileRules($profile))
            ->only(['cnic_number', 'cnic_front_image', 'cnic_back_image', 'photo', 'allow_photo_sharing'])
            ->toArray();

        try {
            $documentData = $request->validate($rules, $this->nikahProfileMessages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->unlockedView($lead, $last7)->withErrors($e->errors());
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

        return $this->unlockedView($lead)->with('status', 'Thank you — your documents have been submitted for verification.');
    }

    // The client-side half of ClientController::requestConsent() — grants
    // or declines a pending request themselves rather than the matchmaker
    // asserting consent happened on their behalf. A real MatchmakingConsent
    // row (method='digital_form') is only created here, at the moment of
    // an actual "grant" — the request row itself never counts toward
    // Lead::hasActiveConsent() while pending.
    public function respondToConsent(Request $request, Lead $lead, MatchmakingConsentRequest $consentRequest)
    {
        $this->assertValidToken($request, $lead);
        abort_unless($consentRequest->lead_id === $lead->id, 404);

        if (!$this->reverifyLast7($lead, $request)) {
            MatchmakingLinkAccess::record($lead, 'consent_response', $request, 'failed');

            return $this->lockedView($lead, 'Your verification expired — please enter the last 7 digits again.', 'آپ کی تصدیق ختم ہو گئی — براہ کرم آخری 7 ہندسے دوبارہ درج کریں۔');
        }

        abort_if(!$consentRequest->isPending(), 422, 'This request has already been responded to.');

        $decision = $request->input('decision');
        abort_unless(in_array($decision, ['grant', 'decline'], true), 422);

        if ($decision === 'grant') {
            MatchmakingConsent::create([
                'lead_id' => $lead->id,
                'consent_type' => $consentRequest->consent_type,
                'method' => 'digital_form',
                'recorded_by' => $consentRequest->requested_by,
                'granted_at' => now(),
            ]);
            $consentRequest->update(['status' => 'granted', 'responded_at' => now()]);
            $label = explode(' — ', MatchmakingConsent::TYPES[$consentRequest->consent_type])[0];
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'consent_recorded', "{$label} consent confirmed by the client themselves via their secure link.");
            $statusMessage = 'Thank you — your consent has been recorded.';
        } else {
            $consentRequest->update(['status' => 'declined', 'responded_at' => now()]);
            $label = explode(' — ', MatchmakingConsent::TYPES[$consentRequest->consent_type])[0];
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'consent_declined', "{$label} consent was declined by the client.");
            $statusMessage = 'Noted — your matchmaker has been informed.';
        }

        MatchmakingLinkAccess::record($lead, 'consent_response', $request, $decision);

        if ($lead->assigned_to) {
            try {
                User::find($lead->assigned_to)?->notify(new MatchmakerConsentResponded($consentRequest, $lead));
            } catch (\Throwable $e) {
                \Log::error('MatchmakerConsentResponded notification failed: ' . $e->getMessage());
            }
        }

        return $this->unlockedView($lead)->with('status', $statusMessage);
    }

    private function reverifyLast7(Lead $lead, Request $request): bool
    {
        $last7 = (string) $request->input('last7');
        $expected = substr(preg_replace('/\D/', '', $lead->phone ?? ''), -7);

        return $expected && $last7 === $expected;
    }

    private function lockedView(Lead $lead, string $error, ?string $errorUr = null): View
    {
        $verifyUrl = route('public.matchmaking.progress.verify', ['lead' => $lead->id, 't' => $lead->progress_link_token]);

        return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'unlocked' => false])
            ->with('error', $error)
            ->with('error_ur', $errorUr);
    }

    private function unlockedView(Lead $lead, ?string $last7 = null): View
    {
        $lead->load(['timelineEvents.matchmaker', 'proposalBatches.proposals.candidate.user', 'nikahProfile', 'consentRequests']);

        $verifyUrl = route('public.matchmaking.progress.verify', ['lead' => $lead->id, 't' => $lead->progress_link_token]);
        $documentsUrl = route('public.matchmaking.progress.documents', ['lead' => $lead->id, 't' => $lead->progress_link_token]);

        return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'documentsUrl' => $documentsUrl, 'unlocked' => true, 'last7' => $last7]);
    }

    private function assertValidToken(Request $request, Lead $lead): void
    {
        abort_unless($lead->progress_link_token && $request->query('t') === $lead->progress_link_token, 403, 'This link is no longer valid — ask your matchmaker for a fresh one. / یہ لنک اب کارآمد نہیں — اپنے میچ میکر سے نیا لنک طلب کریں۔');
    }
}
