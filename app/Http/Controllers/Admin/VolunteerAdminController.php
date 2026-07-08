<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerApplication;
use Illuminate\Http\Request;

class VolunteerAdminController extends Controller
{
    public function index()
    {
        $volunteers = VolunteerApplication::latest()->paginate(15);
        return view('admin.volunteers.index', compact('volunteers'));
    }

    public function approve(VolunteerApplication $volunteer)
    {
        $volunteer->update(['status' => 'approved']);

        if ($volunteer->user_id && !$volunteer->user->hasRole('volunteer')) {
            $volunteer->user->assignRole('volunteer');
        }

        return back()->with('status', 'Approved.');
    }

    public function reject(VolunteerApplication $volunteer)
    {
        $volunteer->update(['status' => 'rejected']);
        return back()->with('status', 'Rejected.');
    }
}
