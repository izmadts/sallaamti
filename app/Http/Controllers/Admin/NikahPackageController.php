<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionRule;
use App\Models\NikahPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Fully admin-editable catalog — every price, limit, and feature line the
// matchmaking packages advertise lives here, not in code. Admin-only
// (route middleware): package pricing is business policy, not a staff
// moderation task like the PermissionCatalog-gated areas.
class NikahPackageController extends Controller
{
    public const COLORS = ['green', 'amber', 'blue', 'red', 'purple', 'teal', 'gray'];

    public function index()
    {
        $packages = NikahPackage::ordered()->withCount('leads')->get();

        return view('admin.nikah-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.nikah-packages.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        NikahPackage::create($validated);

        // A brand-new package has no CommissionRule rows yet — without this,
        // recordPackageCommission() silently no-ops and a matchmaker earns
        // zero commission on it until an admin happens to visit /admin/commissions/rules.
        CommissionRule::ensureSeeded();

        return redirect()->route('admin.nikah-packages.index')->with('status', 'Package created.');
    }

    public function edit(NikahPackage $nikahPackage)
    {
        return view('admin.nikah-packages.edit', ['package' => $nikahPackage]);
    }

    public function update(Request $request, NikahPackage $nikahPackage)
    {
        $validated = $this->validated($request, $nikahPackage);

        $nikahPackage->update($validated);

        return redirect()->route('admin.nikah-packages.index')->with('status', 'Package updated.');
    }

    public function destroy(NikahPackage $nikahPackage)
    {
        abort_if($nikahPackage->leads()->exists(), 422, 'This package is assigned to at least one client — deactivate it instead of deleting, so their history stays intact.');

        $nikahPackage->delete();

        return redirect()->route('admin.nikah-packages.index')->with('status', 'Package deleted.');
    }

    private function validated(Request $request, ?NikahPackage $existing = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ur' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:nikah_packages,slug' . ($existing ? ",{$existing->id}" : '')],
            'tagline' => ['nullable', 'string', 'max:255'],
            'tagline_ur' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:8'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'proposal_limit' => ['nullable', 'integer', 'min:1'],
            'consultant_level' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ur' => ['nullable', 'string', 'max:2000'],
            'features_text' => ['nullable', 'string'],
            'features_text_ur' => ['nullable', 'string'],
            'color' => ['required', 'in:' . implode(',', self::COLORS)],
            'icon' => ['nullable', 'string', 'max:8'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'show_on_public_page' => ['nullable', 'boolean'],
        ]);

        $validated['features'] = collect(explode("\n", $validated['features_text'] ?? ''))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
        unset($validated['features_text']);

        $validated['features_ur'] = collect(explode("\n", $validated['features_text_ur'] ?? ''))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
        unset($validated['features_text_ur']);

        $validated['currency'] = ($validated['currency'] ?? null) ?: 'PKR';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['show_on_public_page'] = $request->boolean('show_on_public_page');

        return $validated;
    }
}
