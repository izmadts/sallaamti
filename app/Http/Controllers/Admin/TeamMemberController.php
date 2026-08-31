<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Services\ImageOptimizer;
use App\Support\HtmlSanitizer;
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
            'name'            => ['required', 'string', 'max:100'],
            'role'            => ['required', 'string', 'max:100'],
            'bio'             => ['nullable', 'string'],
            'photo'           => ['nullable', 'image', 'max:2048'],
            'facebook_url'    => ['nullable', 'url', 'max:255'],
            'instagram_url'   => ['nullable', 'url', 'max:255'],
            'tiktok_url'      => ['nullable', 'url', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
        ]);

        $validated['bio'] = HtmlSanitizer::clean($validated['bio'] ?? null);
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = TeamMember::max('order') + 1;

        // Only one founder shown at a time — making a new member the
        // founder demotes whoever held it before.
        if ($request->has('is_founder')) {
            TeamMember::where('is_founder', true)->update(['is_founder' => false]);
        }
        $validated['is_founder'] = $request->has('is_founder');

        if ($request->hasFile('photo')) {
            $validated['photo'] = ImageOptimizer::store($request->file('photo'), 'team-members', 'public', maxDimension: 800, quality: 82);
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
            'name'            => ['required', 'string', 'max:100'],
            'role'            => ['required', 'string', 'max:100'],
            'bio'             => ['nullable', 'string'],
            'photo'           => ['nullable', 'image', 'max:2048'],
            'facebook_url'    => ['nullable', 'url', 'max:255'],
            'instagram_url'   => ['nullable', 'url', 'max:255'],
            'tiktok_url'      => ['nullable', 'url', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
        ]);

        $validated['bio'] = HtmlSanitizer::clean($validated['bio'] ?? null);
        $validated['is_active'] = $request->has('is_active');

        if ($request->has('is_founder')) {
            TeamMember::where('id', '!=', $team_member->id)->where('is_founder', true)->update(['is_founder' => false]);
        }
        $validated['is_founder'] = $request->has('is_founder');

        if ($request->hasFile('photo')) {
            if ($team_member->photo) Storage::disk('public')->delete($team_member->photo);
            $validated['photo'] = ImageOptimizer::store($request->file('photo'), 'team-members', 'public', maxDimension: 800, quality: 82);
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
