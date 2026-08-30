<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\VolunteerApplication;
use App\Mail\VolunteerApplicationDecision;
use App\Mail\VolunteerApproved;
use App\Notifications\VolunteerApplicationDecided;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VolunteerAdminController extends Controller
{
    public function index()
    {
        $volunteers = VolunteerApplication::latest()->paginate(15);
        return view('admin.volunteers.index', compact('volunteers'));
    }

    public function approve(VolunteerApplication $volunteer)
    {
        if ($volunteer->status === 'approved') {
            return back()->with('status', 'Already approved.');
        }

        $volunteer->update(['status' => 'approved']);

        if ($volunteer->user && !$volunteer->user->hasRole('volunteer')) {
            $volunteer->user->assignRole('volunteer');
        }

        if ($volunteer->user_id) {
            try {
                $certificate = Certificate::firstOrCreate(
                    ['user_id' => $volunteer->user_id, 'type' => 'volunteer_id'],
                    [
                        'course_id' => null,
                        'title' => 'Sallaamti Volunteer ID Card',
                        'certificate_number' => Certificate::generateNumber('VOL'),
                        'issued_by' => Auth::id(),
                        'issued_at' => now(),
                    ]
                );

                Mail::to($volunteer->email)->send(new VolunteerApproved($certificate));
            } catch (\Throwable $e) {
                \Log::error('Volunteer ID card generation/email failed: ' . $e->getMessage());
            }

            try {
                $volunteer->user->notify(new VolunteerApplicationDecided($volunteer, approved: true));
            } catch (\Throwable $e) {
                \Log::error('VolunteerApplicationDecided notification failed: ' . $e->getMessage());
            }
        } elseif ($volunteer->email) {
            // QA finding: a guest applicant (no account, so no Certificate/
            // ID card can be issued) previously got no notification at all
            // on approval — this at least tells them and points them to
            // registering to unlock the ID card.
            try {
                Mail::to($volunteer->email)->send(new VolunteerApplicationDecision($volunteer, approved: true));
            } catch (\Throwable $e) {
                \Log::error('Guest volunteer approval email failed: ' . $e->getMessage());
            }
        }

        return back()->with('status', 'Approved.');
    }

    public function reject(VolunteerApplication $volunteer)
    {
        $volunteer->update(['status' => 'rejected']);

        // QA finding: rejections sent no notification to anyone, registered
        // or guest — an applicant was simply left waiting forever.
        if ($volunteer->email) {
            try {
                Mail::to($volunteer->email)->send(new VolunteerApplicationDecision($volunteer, approved: false));
            } catch (\Throwable $e) {
                \Log::error('Volunteer rejection email failed: ' . $e->getMessage());
            }
        }

        if ($volunteer->user) {
            try {
                $volunteer->user->notify(new VolunteerApplicationDecided($volunteer, approved: false));
            } catch (\Throwable $e) {
                \Log::error('VolunteerApplicationDecided notification failed: ' . $e->getMessage());
            }
        }

        return back()->with('status', 'Rejected.');
    }

    public function destroy(VolunteerApplication $volunteer)
    {
        $volunteer->delete();

        return back()->with('status', 'Volunteer application deleted.');
    }
}
