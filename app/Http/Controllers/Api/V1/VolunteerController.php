<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\VolunteerApplication;
use Barryvdh\DomPDF\Facade\Pdf;
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
    private function myApplication(Request $request): ?VolunteerApplication
    {
        return VolunteerApplication::where('user_id', $request->user()->id)->latest()->first();
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
                $application = VolunteerApplication::create(array_merge($fields, ['user_id' => $user->id]));
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
        $certificate->load('user', 'issuer');

        $pdf = Pdf::loadView('certificates.volunteer-id-card', ['certificate' => $certificate])->setPaper([0, 0, 242.7, 153.15]);

        return $pdf->download('Sallaamti-Volunteer-ID-' . $certificate->certificate_number . '.pdf');
    }
}
