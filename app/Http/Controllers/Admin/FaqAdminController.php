<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query()->orderBy('module')->orderBy('sort_order');

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        $faqs = $query->paginate(20)->withQueryString();

        return view('admin.faqs.index', [
            'faqs' => $faqs,
            'modules' => Faq::MODULES,
            'activeModule' => $request->get('module', ''),
        ]);
    }

    public function create()
    {
        return view('admin.faqs.create', ['modules' => Faq::MODULES]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateFaq($request);

        Faq::create($validated);

        return redirect()->route('admin.faqs.index', ['module' => $validated['module']])
            ->with('status', 'FAQ added.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', ['faq' => $faq, 'modules' => Faq::MODULES]);
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $this->validateFaq($request);

        $faq->update($validated);

        return redirect()->route('admin.faqs.index', ['module' => $validated['module']])
            ->with('status', 'FAQ updated.');
    }

    public function destroy(Faq $faq)
    {
        $module = $faq->module;
        $faq->delete();

        return redirect()->route('admin.faqs.index', ['module' => $module])
            ->with('status', 'FAQ deleted.');
    }

    private function validateFaq(Request $request): array
    {
        $validated = $request->validate([
            'module' => ['required', 'string', 'in:' . implode(',', array_keys(Faq::MODULES))],
            'question_en' => ['required', 'string', 'max:1000'],
            'answer_en' => ['required', 'string'],
            'question_ur' => ['nullable', 'string', 'max:1000'],
            'answer_ur' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        return $validated;
    }
}
