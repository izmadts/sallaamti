<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditorImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        $path = ImageOptimizer::store($request->file('image'), 'editor-uploads', 'public', maxDimension: 1400, quality: 80);

        return response()->json(['url' => Storage::url($path)]);
    }
}
