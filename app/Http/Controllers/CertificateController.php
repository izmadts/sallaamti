<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
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
        abort_unless($certificate->user_id === Auth::id() || Auth::user()->hasRole('admin'), 403);

        $pdf = Pdf::loadView('certificates.pdf', ['certificate' => $certificate->load('user', 'course')])
            ->setPaper('a4', 'landscape');

        return $pdf->download('Sallaamti-Certificate-' . $certificate->certificate_number . '.pdf');
    }

    public function index()
    {
        $certificates = Auth::user()->certificates()->with('course')->latest()->get();
        return view('certificates.index', compact('certificates'));
    }

    public function verify(string $certificateNumber)
    {
        $certificate = Certificate::where('certificate_number', $certificateNumber)->with('user', 'course')->first();
        return view('certificates.verify', compact('certificate'));
    }
    
}
