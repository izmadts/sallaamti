<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateBanner($request);
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $validated['order'] = Banner::max('order') + 1;

        Banner::create($validated);

        return redirect()->route('admin.banners.index')->with('status', 'Banner created.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $this->validateBanner($request, $banner);
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Delete old image if it was an upload (not original seeded img/)
            if (!str_starts_with($banner->image, 'img/')) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('status', 'Banner updated.');
    }

    public function destroy(Banner $banner)
    {
        if (!str_starts_with($banner->image, 'img/')) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();
        return back()->with('status', 'Banner deleted.');
    }

    public function toggle(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return back()->with('status', 'Banner ' . ($banner->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer|exists:banners,id']);

        foreach ($request->order as $position => $id) {
            Banner::where('id', $id)->update(['order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }

    private function validateBanner(Request $request, ?Banner $banner = null): array
    {
        return $request->validate([
            'subtitle'    => ['nullable', 'string', 'max:100'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:50'],
            'button_url'  => ['nullable', 'string', 'max:255'],
            'image'       => [$banner ? 'nullable' : 'required', 'image', 'max:4096'],
        ]);
    }
}
