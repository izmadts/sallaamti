<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DuaRequest;
use Illuminate\Http\Request;

class DuaWallAdminController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => DuaRequest::count(),
            'pending' => DuaRequest::where('status', 'pending')->count(),
            'approved' => DuaRequest::where('status', 'approved')->count(),
            'hidden' => DuaRequest::where('status', 'hidden')->count(),
        ];

        $query = DuaRequest::with('user')->orderByDesc('created_at');

        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $duas = $query->paginate(20)->withQueryString();

        return view('admin.dua-wall.index', compact('duas', 'stats'));
    }

    public function approve(DuaRequest $duaRequest)
    {
        $duaRequest->update(['status' => 'approved', 'rejection_reason' => null]);

        return back()->with('status', 'Dua approved and is now visible on the wall.');
    }

    public function reject(Request $request, DuaRequest $duaRequest)
    {
        $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);

        $duaRequest->update([
            'status' => 'hidden',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('status', 'Dua hidden from the wall.');
    }

    public function destroy(DuaRequest $duaRequest)
    {
        $duaRequest->delete();

        return back()->with('status', 'Dua permanently deleted.');
    }
}
