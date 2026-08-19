<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateAdminController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('user', 'course')->latest()->paginate(15);
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

        $certificate = Certificate::create([
            'user_id' => $validated['user_id'],
            'course_id' => null,
            'type' => 'admin',
            'title' => $validated['title'],
            'certificate_number' => Certificate::generateNumber('CRT'),
            'issued_by' => Auth::id(),
            'issued_at' => now(),
        ]);

        return redirect()->route('certificate.download', $certificate)->with('status', 'Certificate generated.');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return back()->with('status', 'Certificate deleted.');
    }
}
