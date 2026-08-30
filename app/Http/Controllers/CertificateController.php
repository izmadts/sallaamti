<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function generate(Course $course)
    {
        $user = Auth::user();

        abort_unless($course->isEnrolledBy($user), 403);
        abort_unless($course->isCertificateEligibleFor($user), 403, 'Complete the course and pass the final quiz first.');

        $certificate = Certificate::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['certificate_number' => Certificate::generateNumber(), 'issued_at' => now()]
        );

        return redirect()->route('certificate.download', $certificate);
    }

    public function download(Certificate $certificate)
    {
        abort_unless((int) $certificate->user_id === (int) Auth::id() || Auth::user()->hasRole('admin'), 403);

        $certificate->load('user', 'course', 'issuer');

        if ($certificate->type === 'volunteer_id') {
            // 85.6mm x 54mm (ISO/IEC 7810 ID-1) converted to points and
            // rounded up slightly — rounding down here left the page a hair
            // short of the CSS-declared mm size, which made DomPDF spill a
            // near-invisible sliver onto a blank second page.
            $pdf = Pdf::loadView('certificates.volunteer-id-card', ['certificate' => $certificate])
                ->setPaper([0, 0, 242.7, 153.15]);

            return $pdf->download('Sallaamti-Volunteer-ID-' . $certificate->certificate_number . '.pdf');
        }

        if ($certificate->type === 'nikah_counselor_id') {
            $pdf = Pdf::loadView('certificates.nikah-counselor-id', ['certificate' => $certificate])
                ->setPaper([0, 0, 242.7, 153.15]);

            return $pdf->download('Sallaamti-Nikah-Counselor-ID-' . $certificate->certificate_number . '.pdf');
        }

        if ($certificate->course?->track === 'skills') {
            $pdf = Pdf::loadView('certificates.pdf-skills', ['certificate' => $certificate])
                ->setPaper('a4', 'landscape');

            return $pdf->download('Sallaamti-Certificate-' . $certificate->certificate_number . '.pdf');
        }

        $pdf = Pdf::loadView('certificates.pdf', ['certificate' => $certificate])
            ->setPaper('a4', 'landscape');

        return $pdf->download('Sallaamti-Certificate-' . $certificate->certificate_number . '.pdf');
    }

    public function index()
    {
        $certificates = Auth::user()->certificates()->with('course')->latest()->get();
        return view('certificates.index', compact('certificates'));
    }

    public function verify(Request $request, ?string $certificateNumber = null)
    {
        $code = $certificateNumber ?? $request->query('code');
        $certificate = $code ? Certificate::where('certificate_number', $code)->with('user', 'course', 'issuer')->first() : null;

        return view('certificates.verify', compact('certificate', 'code'));
    }

}
