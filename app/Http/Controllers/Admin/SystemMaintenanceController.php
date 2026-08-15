<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SystemMaintenanceController extends Controller
{
    // Every directory user-uploaded images land in, paired with the same
    // max-dimension/quality each type is optimized at on upload (see the
    // ImageOptimizer::store() call sites) — kept in sync with those so a
    // re-run here matches what a fresh upload would produce.
    protected function imageDirectories(): array
    {
        return [
            'avatars' => ['label' => 'Profile Avatars', 'maxDimension' => 512, 'quality' => 82],
            'nikah/photos' => ['label' => 'Nikah Photos', 'maxDimension' => 1200, 'quality' => 82],
            'nikah/cnic' => ['label' => 'Nikah CNIC Images', 'maxDimension' => 1600, 'quality' => 85],
            'nikah/payments' => ['label' => 'Nikah Payment Screenshots', 'maxDimension' => 1600, 'quality' => 82],
            'donations/screenshots' => ['label' => 'Donation Screenshots', 'maxDimension' => 1600, 'quality' => 82],
        ];
    }

    public function index()
    {
        $tables = collect(DB::select('
            SELECT table_name AS table_name,
                   table_rows AS row_count,
                   ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.TABLES
            WHERE table_schema = DATABASE()
            ORDER BY (data_length + index_length) DESC
        '));

        $dbTotalMb = round($tables->sum('size_mb'), 2);

        $imageStats = collect($this->imageDirectories())->map(function ($config, $directory) {
            $files = Storage::disk('private')->allFiles($directory);
            $bytes = array_sum(array_map(fn ($f) => Storage::disk('private')->size($f), $files));

            return array_merge($config, [
                'directory' => $directory,
                'fileCount' => count($files),
                'sizeMb' => round($bytes / 1024 / 1024, 2),
            ]);
        })->values();

        $imagesTotalMb = round($imageStats->sum('sizeMb'), 2);

        return view('admin.maintenance.index', compact('tables', 'dbTotalMb', 'imageStats', 'imagesTotalMb'));
    }

    // OPTIMIZE TABLE defragments and reclaims unused space (e.g. after lots
    // of deletes/updates) — it rebuilds each table in place but never
    // touches the rows themselves, so this is safe to run anytime.
    public function optimizeDatabase()
    {
        $tables = collect(DB::select('SHOW TABLES'))
            ->map(fn ($row) => array_values((array) $row)[0]);

        $before = (float) DB::selectOne('
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS mb
            FROM information_schema.TABLES WHERE table_schema = DATABASE()
        ')->mb;

        foreach ($tables as $table) {
            try {
                DB::statement("OPTIMIZE TABLE `{$table}`");
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $after = (float) DB::selectOne('
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS mb
            FROM information_schema.TABLES WHERE table_schema = DATABASE()
        ')->mb;

        $reclaimed = round($before - $after, 2);

        return back()->with('status', "Optimized {$tables->count()} database table(s)."
            . ($reclaimed > 0 ? " Reclaimed {$reclaimed} MB." : ' Already compact — nothing to reclaim.'));
    }

    // Re-runs every already-uploaded image through the same optimizer new
    // uploads go through — for anything uploaded before that existed.
    // Never touches file content it can't safely shrink further.
    public function optimizeImages()
    {
        set_time_limit(300);

        $processed = 0;
        $skipped = 0;
        $bytesSaved = 0;

        foreach ($this->imageDirectories() as $directory => $config) {
            $stats = ImageOptimizer::reoptimizeDirectory($directory, 'private', $config['maxDimension'], $config['quality']);

            $processed += $stats['processed'];
            $skipped += $stats['skipped'];
            $bytesSaved += $stats['bytesBefore'] - $stats['bytesAfter'];
        }

        $savedMb = round($bytesSaved / 1024 / 1024, 2);

        return back()->with('status', "Re-optimized {$processed} image(s), saved {$savedMb} MB."
            . ($skipped > 0 ? " {$skipped} file(s) skipped (couldn't be read as images)." : ''));
    }
}
