<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityPostController extends Controller
{
    public function index()
    {
        $activityPosts = ActivityPost::orderBy('order')->get();
        return view('admin.activity-posts.index', compact('activityPosts'));
    }

    public function create()
    {
        return view('admin.activity-posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:150'],
            'description'   => ['required', 'string'],
            'activity_date' => ['nullable', 'date'],
            'photo'         => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = ActivityPost::max('order') + 1;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('activity-posts', 'public');
        }

        ActivityPost::create($validated);

        return redirect()->route('admin.activity-posts.index')->with('status', 'Activity added.');
    }

    public function edit(ActivityPost $activity_post)
    {
        return view('admin.activity-posts.edit', ['post' => $activity_post]);
    }

    public function update(Request $request, ActivityPost $activity_post)
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:150'],
            'description'   => ['required', 'string'],
            'activity_date' => ['nullable', 'date'],
            'photo'         => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('photo')) {
            if ($activity_post->photo) Storage::disk('public')->delete($activity_post->photo);
            $validated['photo'] = $request->file('photo')->store('activity-posts', 'public');
        }

        $activity_post->update($validated);

        return redirect()->route('admin.activity-posts.index')->with('status', 'Activity updated.');
    }

    public function destroy(ActivityPost $activity_post)
    {
        if ($activity_post->photo) Storage::disk('public')->delete($activity_post->photo);
        $activity_post->delete();
        return back()->with('status', 'Activity deleted.');
    }

    public function toggle(ActivityPost $activity_post)
    {
        $activity_post->update(['is_active' => !$activity_post->is_active]);
        return back()->with('status', 'Updated.');
    }
}
