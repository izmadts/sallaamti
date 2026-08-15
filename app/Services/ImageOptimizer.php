<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

// Resizes and re-encodes user-uploaded images before saving them to disk —
// phone camera photos routinely arrive at 8-12MB, far larger than anything
// this site ever displays. Re-encoding to JPEG also strips EXIF metadata
// (GPS location, device info) as a side effect, which is a privacy win for
// member photos. Falls back to storing the original untouched if the file
// can't be decoded, so an upload never fails outright because of this.
class ImageOptimizer
{
    protected static ?ImageManager $manager = null;

    public static function store(UploadedFile $file, string $directory, string $disk = 'private', int $maxDimension = 1600, int $quality = 82): string
    {
        try {
            $image = static::manager()->read($file->getRealPath())
                ->scaleDown(width: $maxDimension, height: $maxDimension);

            $filename = $directory . '/' . Str::random(40) . '.jpg';

            Storage::disk($disk)->put($filename, (string) $image->toJpeg(quality: $quality));

            return $filename;
        } catch (\Throwable $e) {
            report($e);

            return $file->store($directory, $disk);
        }
    }

    protected static function manager(): ImageManager
    {
        return static::$manager ??= new ImageManager(new Driver());
    }
}
