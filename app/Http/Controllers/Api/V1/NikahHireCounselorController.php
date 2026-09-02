<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadClientMessage;
use App\Models\MatchmakerApplication;
use App\Models\MatchmakingTimelineEvent;
use App\Models\NikahPackage;
use App\Models\User;
use App\Notifications\LeadClientMessageReceived;
use App\Notifications\MatchmakerClientHired;
use App\Notifications\NewPackagePaymentSubmitted;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// The bridge from self-service (a member with their own NikahProfile) into
// the counselor-assisted flow (a Lead, packages, proposals) — entirely
// optional, started by the member from within the app, never forced. Once
// hired, the resulting Lead is a normal client in the counselor's existing
// CRM (matchmaker/clients/*) — nothing counselor-side changes except
// messaging, which needs its own channel (see messages() below).
class NikahHireCounselorController extends Controller
{
    private function myProfile(Request $request)
    {
        $profile = $request->user()->nikahProfile;
        abort_unless($profile, 409, __('db.Create your Nikah profile first.'));
        return $profile;
    }

    public function counselors(Request $request): JsonResponse
    {
        $city = trim((string) $request->query('city', ''));
        $gender = $request->query('gender');

        $applications = MatchmakerApplication::where('status', 'certified')
            ->with('user')
            ->get()
            ->filter(fn (MatchmakerApplication $app) => $app->user && $app->user->hasRole('matchmaker'))
            ->when($city !== '', fn ($apps) => $apps->filter(
                fn (MatchmakerApplication $app) => $app->user->city && str_contains(strtolower($app->user->city), strtolower($city))
            ))
            ->when(in_array($gender, ['male', 'female'], true), fn ($apps) => $apps->filter(
                fn (MatchmakerApplication $app) => $app->user->gender === $gender
            ));

        return response()->json([
            'counselors' => $applications->map(fn (MatchmakerApplication $app) => [
                'id' => $app->user->id,
                'name' => $app->user->name,
                'avatar' => $app->user->apiAvatarUrl(),
                'bio' => $app->user->counselor_bio,
                'tier' => $app->level,
                'city' => $app->user->city,
                'gender' => $app->user->gender,
            ])->values(),
        ]);
    }

    // Creates the Lead the first time a member hires a counselor (skipping
    // straight past the "registration" stage — they're already a full
    // member with a verified-or-verifying NikahProfile), or just
    // reassigns it if one already exists (e.g. they're switching
    // counselors, or had one from an earlier, unrelated contact).
    public function hire(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'counselor_id' => ['required', 'exists:users,id'],
        ]);

        $counselor = User::findOrFail($validated['counselor_id']);
        abort_unless($counselor->hasRole('matchmaker'), 422, 'That account is not a Nikah Counselor.');

        $profile = $this->myProfile($request);
        $user = $request->user();

        $lead = Lead::where('nikah_profile_id', $profile->id)->first();

        if ($lead) {
            $lead->update(['assigned_to' => $counselor->id]);
        } else {
            $lead = Lead::create([
                'name' => $user->name,
                'gender' => $user->gender,
                'phone' => $user->phone,
                'email' => $user->email,
                'looking_for' => 'self',
                'source' => 'other',
                'assigned_to' => $counselor->id,
                'nikah_profile_id' => $profile->id,
                'created_by' => $user->id,
            ]);
        }

        MatchmakingTimelineEvent::log($lead, $profile, 'counselor_hired', "{$user->name} chose {$counselor->name} as their Nikah Counselor from the app.");

        try {
            $counselor->notify(new MatchmakerClientHired($lead));
        } catch (\Throwable $e) {
            \Log::error('MatchmakerClientHired notification failed: ' . $e->getMessage());
        }

        return response()->json(['lead' => $this->leadPayload($lead)], 201);
    }

    public function myLead(Request $request): JsonResponse
    {
        $profile = $this->myProfile($request);
        $lead = Lead::where('nikah_profile_id', $profile->id)->whereNotNull('assigned_to')->first();

        return response()->json(['lead' => $lead ? $this->leadPayload($lead) : null]);
    }

    // Lets a member undo hire() themselves and fall back to self-service —
    // only while there's no payment in flight for this counselor. Once a
    // package payment is submitted or confirmed, releasing here would strand
    // that transaction (admin's confirm flow attributes commission off
    // assigned_to) or silently abandon money already sent, so that has to
    // go through admin/support instead.
    public function release(Request $request): JsonResponse
    {
        $profile = $this->myProfile($request);
        $lead = Lead::where('nikah_profile_id', $profile->id)->whereNotNull('assigned_to')->first();
        abort_unless($lead, 409, 'You have not hired a Nikah Counselor.');
        abort_if(
            in_array($lead->package_payment_status, ['submitted', 'confirmed'], true),
            422,
            'You have a package payment in progress with this counselor — contact support to change counselors.'
        );

        $counselorName = $lead->assignedTo?->name ?? 'your counselor';
        $lead->update(['assigned_to' => null]);

        MatchmakingTimelineEvent::log($lead, $profile, 'counselor_released', "{$request->user()->name} went back to self-service instead of {$counselorName}.");

        return response()->json(['message' => __('db.You\'re back to self-service.')]);
    }

    private function leadPayload(Lead $lead): array
    {
        $lead->loadMissing('assignedTo');

        return [
            'id' => $lead->id,
            'counselor' => $lead->assignedTo ? [
                'id' => $lead->assignedTo->id,
                'name' => $lead->assignedTo->name,
                'avatar' => $lead->assignedTo->apiAvatarUrl(),
            ] : null,
            'nikah_package_id' => $lead->nikah_package_id,
            'package_payment_status' => $lead->package_payment_status,
            'package_payment_rejection_reason' => $lead->package_payment_rejection_reason,
        ];
    }

    public function packages(): JsonResponse
    {
        return response()->json([
            'packages' => NikahPackage::active()->ordered()->get()->reject->isOneTime()->values()->map(fn (NikahPackage $p) => [
                'id' => $p->id,
                'name' => $p->localizedName(),
                'tagline' => $p->localizedTagline(),
                'description' => $p->localizedDescription(),
                'features' => $p->localizedFeatures(),
                'price' => $p->price,
                'currency' => $p->currency,
                'duration_days' => $p->duration_days,
                'proposal_limit' => $p->proposal_limit,
            ]),
            'payment_instructions' => [
                'jazzcash_number' => setting('jazzcash_number'),
                'jazzcash_account_title' => setting('jazzcash_account_title'),
                'easypaisa_number' => setting('easypaisa_number'),
                'bank_name' => setting('bank_name'),
                'bank_account_title' => setting('bank_account_title'),
                'bank_account_number' => setting('bank_account_number'),
                'bank_account_iban' => setting('bank_account_iban'),
            ],
        ]);
    }

    // Mirrors Public\MatchmakingProgressController::selectPackage() exactly
    // (same fields, same "submitted, awaiting admin review" flow) —
    // Sanctum-authenticated instead of signed-link + last-7-digits, since
    // the member is already signed into the app.
    public function submitPackage(Request $request): JsonResponse
    {
        $profile = $this->myProfile($request);
        $lead = Lead::where('nikah_profile_id', $profile->id)->whereNotNull('assigned_to')->first();
        abort_unless($lead, 409, 'Hire a Nikah Counselor first.');
        abort_if($lead->package_payment_status === 'submitted', 422, 'A package payment is already awaiting review.');

        $validated = $request->validate([
            'nikah_package_id' => ['required', 'exists:nikah_packages,id'],
            'payment_method' => ['required', 'in:jazzcash,bank_transfer,easypaisa'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'payment_screenshot' => ['required', 'image', 'max:4096'],
        ]);

        $package = NikahPackage::active()->findOrFail($validated['nikah_package_id']);
        abort_if($package->isOneTime(), 422, 'That package is not a Nikah counseling package.');

        $screenshotPath = ImageOptimizer::store($request->file('payment_screenshot'), 'nikah/payments', 'private');

        $lead->update([
            'pending_package_id' => $package->id,
            'package_payment_method' => $validated['payment_method'],
            'package_payment_reference' => $validated['payment_reference'] ?? null,
            'package_payment_screenshot' => $screenshotPath,
            'package_payment_status' => 'submitted',
            'package_payment_rejection_reason' => null,
        ]);

        MatchmakingTimelineEvent::log($lead, $profile, 'package_payment_submitted', "Client selected the {$package->name} package and submitted payment proof.");

        User::role('admin')->each(function ($admin) use ($lead) {
            try {
                $admin->notify(new NewPackagePaymentSubmitted($lead));
            } catch (\Throwable $e) {
                \Log::error('NewPackagePaymentSubmitted notification failed: ' . $e->getMessage());
            }
        });

        return response()->json(['lead' => $this->leadPayload($lead->fresh())]);
    }

    private function authorizeLeadParty(Request $request, Lead $lead): void
    {
        $userId = $request->user()->id;
        $isClient = $lead->nikahProfile && (int) $lead->nikahProfile->user_id === (int) $userId;
        $isCounselor = (int) $lead->assigned_to === (int) $userId;

        abort_unless($isClient || $isCounselor, 403);
    }

    public function messages(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeLeadParty($request, $lead);

        $messages = $lead->clientMessages()->with('sender')->get();

        $lead->clientMessages()
            ->where('sender_id', '!=', $request->user()->id)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages->map(fn (LeadClientMessage $m) => [
                'id' => $m->id,
                'message' => $m->message,
                'is_mine' => (int) $m->sender_id === (int) $request->user()->id,
                'sender_name' => $m->sender?->name,
                'created_at' => $m->created_at->toIso8601String(),
            ]),
        ]);
    }

    public function sendMessage(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeLeadParty($request, $lead);

        $request->validate(['message' => ['required', 'string', 'max:1000']]);

        $message = LeadClientMessage::create([
            'lead_id' => $lead->id,
            'sender_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        $recipientId = (int) $lead->assigned_to === (int) $request->user()->id
            ? $lead->nikahProfile?->user_id
            : $lead->assigned_to;
        $recipient = $recipientId ? User::find($recipientId) : null;

        if ($recipient) {
            try {
                $recipient->notify(new LeadClientMessageReceived($message));
            } catch (\Throwable $e) {
                \Log::error('LeadClientMessageReceived notification failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'is_mine' => true,
                'sender_name' => $request->user()->name,
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ], 201);
    }
}
