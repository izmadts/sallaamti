<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use App\Notifications\CourseCertificateDecided;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateAdminController extends Controller
{
    public function index()
    {
        // Pending requests surface first regardless of when they were
        // created — that's the actionable queue; everything already
        // decided is a historical record admin rarely needs to scroll to.
        $certificates = Certificate::with('user', 'course')
            ->orderByRaw("status = 'pending' desc")
            ->latest()
            ->paginate(15);

        return view('admin.certificates.index', compact('certificates'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.certificates.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:150'],
        ]);

        // Admin issuing one directly is itself the approval — there's no
        // separate decision left to make on a row admin just created.
        $certificate = Certificate::create([
            'user_id' => $validated['user_id'],
            'course_id' => null,
            'type' => 'admin',
            'title' => $validated['title'],
            'status' => 'approved',
            'certificate_number' => Certificate::generateNumber('CRT'),
            'issued_by' => Auth::id(),
            'issued_at' => now(),
        ]);

        return redirect()->route('certificate.download', $certificate)->with('status', 'Certificate generated.');
    }

    /**
     * A course-completion request the student submitted — assigns the real
     * certificate_number/issued_at now, since neither existed while pending
     * (see the migration this is built on for why: a rejected or still-
     * pending request should never have been a real, publicly verifiable
     * certificate).
     */
    public function approve(Certificate $certificate)
    {
        if (!$certificate->isPending()) {
            return back()->with('status', 'Already reviewed.');
        }

        $certificate->update([
            'status' => 'approved',
            'certificate_number' => Certificate::generateNumber(),
            'issued_by' => Auth::id(),
            'issued_at' => now(),
        ]);

        try {
            $certificate->user->notify(new CourseCertificateDecided($certificate, approved: true));
        } catch (\Throwable $e) {
            \Log::error('CourseCertificateDecided (approved) notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Certificate approved.');
    }

    public function reject(Request $request, Certificate $certificate)
    {
        if (!$certificate->isPending()) {
            return back()->with('status', 'Already reviewed.');
        }

        $validated = $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);

        $certificate->update(['status' => 'rejected', 'rejection_reason' => $validated['rejection_reason']]);

        try {
            $certificate->user->notify(new CourseCertificateDecided($certificate, approved: false));
        } catch (\Throwable $e) {
            \Log::error('CourseCertificateDecided (rejected) notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Certificate request rejected.');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return back()->with('status', 'Certificate deleted.');
    }
}
