<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Services\CertificatePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return CertificatePdf::download($certificate);
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
