<?php

namespace App\Services;

use getID3;
use Illuminate\Http\UploadedFile;

// Reads a video's duration straight from its container metadata (mp4/mov/
// webm box headers) via getID3 — pure PHP, no ffmpeg binary needed, so this
// works on shared hosting that can't install one. Used to cap uploads to
// "Reel length" instead of actually compressing them (compression needs
// ffmpeg; this app's hosts don't reliably have it — see
// CommunityPostController's media validation).
class VideoDuration
{
    /** Seconds, or null if it couldn't be read (never blocks the upload on its own). */
    public static function seconds(UploadedFile $file): ?float
    {
        try {
            $info = (new getID3())->analyze($file->getRealPath());

            return $info['playtime_seconds'] ?? null;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}
