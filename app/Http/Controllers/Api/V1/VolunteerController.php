<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\VolunteerApplication;
use App\Services\CertificatePdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

// The mobile-app side of VolunteerController (public web form) and
// VolunteerAdminController (admin review). The web form is guest-oriented
// and has no status/re-apply concept; here the applicant is always a known
// account, so apply() finds-or-updates their own application instead of
// blindly inserting a new row, and status()/certificate() let them check
// in without waiting on the approval email.
class VolunteerController extends Controller
{
    // Matches by user_id first, but also falls back to email/phone — the
    // public web form (VolunteerController@store) lets anyone submit
    // without an account, so a returning member's own earlier application
    // may not be linked to their user_id yet. Without this fallback,
    // apply() wouldn't find that row and would collide with its unique
    // email/phone constraint on insert instead of recognizing it.
    private function myApplication(Request $request): ?VolunteerApplication
    {
        $user = $request->user();

        return VolunteerApplication::where(function ($query) use ($user) {
            $query->where('user_id', $user->id);
            if ($user->email) {
                $query->orWhere('email', $user->email);
            }
            if ($user->phone) {
                $query->orWhere('phone', $user->phone);
            }
        })->latest()->first();
    }

    private function payload(VolunteerApplication $application): array
    {
        return [
            'id' => $application->id,
            'status' => $application->status,
            'city' => $application->city,
            'area_of_interest' => $application->area_of_interest,
            'message' => $application->message,
            'created_at' => $application->created_at,
            'has_id_card' => $application->status === 'approved',
        ];
    }

    public function status(Request $request): JsonResponse
    {
        $application = $this->myApplication($request);

        return response()->json(['application' => $application ? $this->payload($application) : null]);
    }

    public function apply(Request $request): JsonResponse
    {
        $existing = $this->myApplication($request);
        abort_if($existing && $existing->status === 'pending', 409, 'Your application is already awaiting review.');
        abort_if($existing && $existing->status === 'approved', 409, 'You are already an approved volunteer.');

        $validated = $request->validate([
            'city' => ['nullable', 'string', 'max:100'],
            'area_of_interest' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $fields = array_merge($validated, [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => 'pending',
        ]);

        try {
            if ($existing) {
                $existing->update($fields);
                $application = $existing;
            } else {
                $application = VolunteerApplication::create($fields);
            }
        } catch (\Illuminate\Database\QueryException) {
            throw ValidationException::withMessages([
                'email' => 'This email or phone number is already on a volunteer application. Please contact support.',
            ]);
        }

        return response()->json(['application' => $this->payload($application)], 201);
    }

    public function certificate(Request $request): Response
    {
        $certificate = Certificate::where('user_id', $request->user()->id)->where('type', 'volunteer_id')->firstOrFail();

        return CertificatePdf::download($certificate);
    }
}
