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

            // Storage disks in this app are configured with 'throw' => false,
            // so a failed write (permissions, disk full) returns false here
            // instead of throwing — without this check that failure was
            // silently ignored and a path to a file that was never written
            // got saved to the database, leaving a broken image forever.
            if (!Storage::disk($disk)->put($filename, (string) $image->toJpeg(quality: $quality))) {
                throw new \RuntimeException("Failed to write optimized image to disk [{$disk}] at [{$filename}].");
            }

            return $filename;
        } catch (\Throwable $e) {
            report($e);

            $path = $file->store($directory, $disk);

            if ($path === false) {
                throw new \RuntimeException("Failed to store uploaded file to disk [{$disk}] in [{$directory}] — check disk permissions and free space.");
            }

            return $path;
        }
    }

    // Re-processes every image already sitting in a directory, in place —
    // same path, same filename, so nothing else referencing it (DB rows,
    // other code) needs to change. Used by the admin Maintenance tool to
    // shrink files that were uploaded before this optimizer existed. Only
    // ever overwrites a file with a *smaller* result, and skips (never
    // deletes or breaks) anything it can't decode — safe to re-run anytime.
    public static function reoptimizeDirectory(string $directory, string $disk, int $maxDimension, int $quality): array
    {
        $stats = ['processed' => 0, 'skipped' => 0, 'bytesBefore' => 0, 'bytesAfter' => 0];

        foreach (Storage::disk($disk)->allFiles($directory) as $path) {
            $before = Storage::disk($disk)->size($path);

            try {
                $encoded = (string) static::manager()->read(Storage::disk($disk)->path($path))
                    ->scaleDown(width: $maxDimension, height: $maxDimension)
                    ->toJpeg(quality: $quality);

                $after = strlen($encoded);

                if ($after < $before) {
                    Storage::disk($disk)->put($path, $encoded);
                } else {
                    $after = $before;
                }

                $stats['processed']++;
                $stats['bytesBefore'] += $before;
                $stats['bytesAfter'] += $after;
            } catch (\Throwable $e) {
                report($e);
                $stats['skipped']++;
            }
        }

        return $stats;
    }

    protected static function manager(): ImageManager
    {
        return static::$manager ??= new ImageManager(new Driver());
    }
}
