<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index()
    {
        $teamMembers = TeamMember::orderBy('order')->get();
        return view('admin.team-members.index', compact('teamMembers'));
    }

    public function create()
    {
        return view('admin.team-members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'role'  => ['required', 'string', 'max:100'],
            'bio'   => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = TeamMember::max('order') + 1;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('team-members', 'public');
        }

        TeamMember::create($validated);

        return redirect()->route('admin.team-members.index')->with('status', 'Team member added.');
    }

    public function edit(TeamMember $team_member)
    {
        return view('admin.team-members.edit', ['teamMember' => $team_member]);
    }

    public function update(Request $request, TeamMember $team_member)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'role'  => ['required', 'string', 'max:100'],
            'bio'   => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('photo')) {
            if ($team_member->photo) Storage::disk('public')->delete($team_member->photo);
            $validated['photo'] = $request->file('photo')->store('team-members', 'public');
        }

        $team_member->update($validated);

        return redirect()->route('admin.team-members.index')->with('status', 'Team member updated.');
    }

    public function destroy(TeamMember $team_member)
    {
        if ($team_member->photo) Storage::disk('public')->delete($team_member->photo);
        $team_member->delete();
        return back()->with('status', 'Team member deleted.');
    }

    public function toggle(TeamMember $team_member)
    {
        $team_member->update(['is_active' => !$team_member->is_active]);
        return back()->with('status', 'Updated.');
    }
}
