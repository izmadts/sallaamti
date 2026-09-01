<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Services\CertificatePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Submits a request rather than issuing a certificate outright — an
     * admin has to approve it first (Admin\CertificateAdminController::
     * approve()) before certificate_number/issued_at exist. (user_id,
     * course_id) is unique, so a previously-rejected request is reset back
     * to pending rather than left rejected forever the way firstOrCreate
     * would leave it.
     */
    public function generate(Course $course)
    {
        $user = Auth::user();

        abort_unless($course->isEnrolledBy($user), 403);
        abort_unless($course->isCertificateEligibleFor($user), 403, 'Complete the course and pass the final quiz first.');

        $certificate = Certificate::firstOrNew(['user_id' => $user->id, 'course_id' => $course->id]);

        if ($certificate->isApproved()) {
            return redirect()->route('certificate.download', $certificate);
        }
        abort_if($certificate->isPending() && $certificate->exists, 422, 'Your request is already awaiting review.');

        $certificate->fill(['type' => 'course', 'status' => 'pending', 'rejection_reason' => null])->save();

        return redirect()->route('certificate.index')
            ->with('status', 'Certificate requested! You\'ll be notified once our team reviews it.');
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
