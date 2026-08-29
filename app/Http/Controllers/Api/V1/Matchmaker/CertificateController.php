<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\MatchmakerApplication;
use App\Models\User;
use App\Notifications\NikahCounselorCardRequested;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Lets a certified counselor pull their own ID-card PDF straight from the
// app, see their mailing address on file, and ask admin to print and mail
// the physical card — that printing/shipping step stays a manual admin
// action (Admin\MatchmakerApplicationController creates the Certificate
// row itself on certification); this just gives instant digital access to
// the same document plus a way to ask for the physical one.
class CertificateController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $certificate = Certificate::where('user_id', $request->user()->id)->where('type', 'nikah_counselor_id')->first();
        $application = MatchmakerApplication::where('user_id', $request->user()->id)->where('status', 'certified')->first();

        return response()->json([
            'has_certificate' => $certificate !== null,
            'certificate_number' => $certificate?->certificate_number,
            'issued_at' => $certificate?->issued_at?->toIso8601String(),
            'mailing_address' => $application ? trim(collect([$application->address, $application->area, $application->country])->filter()->implode(', ')) : null,
            'card_requested_at' => $application?->card_requested_at?->toIso8601String(),
            'card_dispatched_at' => $application?->card_dispatched_at?->toIso8601String(),
        ]);
    }

    public function requestDispatch(Request $request): JsonResponse
    {
        $application = MatchmakerApplication::where('user_id', $request->user()->id)->where('status', 'certified')->first();
        abort_unless($application, 422, 'No certified application on file.');

        $application->update(['card_requested_at' => now()]);

        User::role('admin')->each(function ($admin) use ($application) {
            try {
                $admin->notify(new NikahCounselorCardRequested($application));
            } catch (\Throwable $e) {
                \Log::error('NikahCounselorCardRequested notification failed: ' . $e->getMessage());
            }
        });

        return response()->json(['status' => 'ok', 'card_requested_at' => $application->card_requested_at->toIso8601String()]);
    }

    public function download(Request $request): Response
    {
        $certificate = Certificate::where('user_id', $request->user()->id)->where('type', 'nikah_counselor_id')->firstOrFail();
        $certificate->load('user', 'issuer');

        $pdf = Pdf::loadView('certificates.nikah-counselor-id', ['certificate' => $certificate])
            ->setPaper([0, 0, 242.7, 153.15]);

        return $pdf->download('Sallaamti-Nikah-Counselor-ID-' . $certificate->certificate_number . '.pdf');
    }
}
