<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesNikahFileAccess;
use App\Models\NikahProfile;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NikahFileController extends Controller
{
    use AuthorizesNikahFileAccess;

    public function show(NikahProfile $profile, string $type): StreamedResponse
    {
        return $this->resolveNikahFile($profile, $type, Auth::user());
    }
}
