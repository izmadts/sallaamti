<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Deliberately does NOT reuse Concerns\AuthorizesNikahFileAccess — that
// trait's ownership check is `$profile->user_id === $user->id`, which would
// 403 a matchmaker viewing their own walk-in client's CNIC/photo (the
// profile's user_id is the CLIENT's account, never the matchmaker's). A
// matchmaker's access to a client they registered or are assigned to is
// legitimate — they collected the documents in person — so this serves
// directly after its own Lead-scoped authorization instead.
class ClientFileController extends Controller
{
    public function show(Lead $lead, string $type): StreamedResponse
    {
        abort_unless(auth()->user()->hasRole('admin') || auth()->user()->can('leads.manage') || (int) $lead->assigned_to === (int) auth()->id(), 403, 'This client is assigned to another Nikah Counselor, so it is hidden from your account for privacy. If this client should be yours, ask your admin to reassign it to you.');

        $profile = $lead->nikahProfile;
        abort_unless($profile, 404);

        abort_unless(in_array($type, ['photo', 'cnic_front_image', 'cnic_back_image', 'payment_screenshot']), 404);

        $path = $profile->{$type};
        abort_unless($path && Storage::disk('private')->exists($path), 404);

        return Storage::disk('private')->response($path);
    }
}
