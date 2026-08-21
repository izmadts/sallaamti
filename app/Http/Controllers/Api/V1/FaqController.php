<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'module' => ['nullable', 'string', 'in:' . implode(',', array_keys(Faq::MODULES))],
        ]);

        $locale = $request->get('locale', app()->getLocale());
        $locale = in_array($locale, ['en', 'ur'], true) ? $locale : 'en';

        $query = Faq::active()->orderBy('module')->orderBy('sort_order');

        if ($request->filled('module')) {
            $query->forModule($request->module);
        }

        $faqs = $query->get()->map(fn (Faq $faq) => [
            'id' => $faq->id,
            'module' => $faq->module,
            'question' => $faq->question($locale),
            'answer' => $faq->answer($locale),
        ]);

        return response()->json(['faqs' => $faqs]);
    }
}
