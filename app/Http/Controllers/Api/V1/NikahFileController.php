<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\AuthorizesNikahFileAccess;
use App\Http\Controllers\Controller;
use App\Models\NikahProfile;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NikahFileController extends Controller
{
    use AuthorizesNikahFileAccess;

    public function show(Request $request, NikahProfile $profile, string $type): StreamedResponse
    {
        return $this->resolveNikahFile($profile, $type, $request->user());
    }
}
