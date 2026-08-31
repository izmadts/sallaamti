<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SystemMaintenanceController extends Controller
{
    // Every directory user-uploaded images land in, paired with the same
    // disk/max-dimension/quality each type is optimized at on upload (see
    // the ImageOptimizer::store() call sites) — kept in sync with those so
    // a re-run here matches what a fresh upload would produce.
    protected function imageDirectories(): array
    {
        return [
            'avatars' => ['label' => 'Profile Avatars', 'disk' => 'private', 'maxDimension' => 512, 'quality' => 82],
            'nikah/photos' => ['label' => 'Nikah Photos', 'disk' => 'private', 'maxDimension' => 1200, 'quality' => 82],
            'nikah/cnic' => ['label' => 'Nikah CNIC Images', 'disk' => 'private', 'maxDimension' => 1600, 'quality' => 85],
            'nikah/payments' => ['label' => 'Nikah Payment Screenshots', 'disk' => 'private', 'maxDimension' => 1600, 'quality' => 82],
            'donations/screenshots' => ['label' => 'Donation Screenshots', 'disk' => 'private', 'maxDimension' => 1600, 'quality' => 82],
            'quran-live/payments' => ['label' => 'Quran Live Payment Screenshots', 'disk' => 'private', 'maxDimension' => 1600, 'quality' => 82],
            'wall/duas' => ['label' => 'Wall Dua Photos', 'disk' => 'public', 'maxDimension' => 1080, 'quality' => 75],
            'community-posts' => ['label' => 'Community Post Photos', 'disk' => 'public', 'maxDimension' => 1600, 'quality' => 82],
            'banners' => ['label' => 'Site Banners', 'disk' => 'public', 'maxDimension' => 1920, 'quality' => 82],
            'blog-posts' => ['label' => 'Blog Cover Images', 'disk' => 'public', 'maxDimension' => 1600, 'quality' => 82],
            'posts' => ['label' => 'Member Post Cover Images', 'disk' => 'public', 'maxDimension' => 1600, 'quality' => 82],
            'testimonials' => ['label' => 'Testimonial Photos', 'disk' => 'public', 'maxDimension' => 600, 'quality' => 82],
            'team-members' => ['label' => 'Team Member Photos', 'disk' => 'public', 'maxDimension' => 800, 'quality' => 82],
            'settings' => ['label' => 'Settings Images (SEO/OG)', 'disk' => 'public', 'maxDimension' => 1200, 'quality' => 82],
            'editor-uploads' => ['label' => 'Rich-Text Editor Images', 'disk' => 'public', 'maxDimension' => 1400, 'quality' => 80],
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
            $disk = Storage::disk($config['disk']);
            $files = $disk->allFiles($directory);
            $bytes = array_sum(array_map(fn ($f) => $disk->size($f), $files));

            return array_merge($config, [
                'directory' => $directory,
                'fileCount' => count($files),
                'sizeMb' => round($bytes / 1024 / 1024, 2),
            ]);
        })->values();

        $imagesTotalMb = round($imageStats->sum('sizeMb'), 2);

        $logFiles = collect(glob(storage_path('logs/*.log')))->map(fn ($path) => [
            'name' => basename($path),
            'sizeMb' => round(filesize($path) / 1024 / 1024, 2),
            'modifiedAt' => \Illuminate\Support\Carbon::createFromTimestamp(filemtime($path)),
        ])->sortByDesc('modifiedAt')->values();
        $logsTotalMb = round($logFiles->sum('sizeMb'), 2);

        return view('admin.maintenance.index', compact('tables', 'dbTotalMb', 'imageStats', 'imagesTotalMb', 'logFiles', 'logsTotalMb'));
    }

    // Deletes every storage/logs/*.log file. Safe to run anytime — nothing
    // in the app reads its own log files back; they exist purely for a
    // human to inspect after the fact. Since switching LOG_STACK to
    // 'daily', new files rotate out on their own (see LOG_DAILY_DAYS in
    // .env) — this is for clearing out whatever already accumulated before
    // that, plus a manual escape hatch if disk space is ever tight.
    public function clearLogs()
    {
        $files = glob(storage_path('logs/*.log'));
        $freedBytes = array_sum(array_map('filesize', $files));

        foreach ($files as $file) {
            @unlink($file);
        }

        $freedMb = round($freedBytes / 1024 / 1024, 2);

        return back()->with('status', count($files) > 0
            ? 'Cleared ' . count($files) . " log file(s), freed {$freedMb} MB."
            : 'No log files to clear.');
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
            $stats = ImageOptimizer::reoptimizeDirectory($directory, $config['disk'], $config['maxDimension'], $config['quality']);

            $processed += $stats['processed'];
            $skipped += $stats['skipped'];
            $bytesSaved += $stats['bytesBefore'] - $stats['bytesAfter'];
        }

        $savedMb = round($bytesSaved / 1024 / 1024, 2);

        return back()->with('status', "Re-optimized {$processed} image(s), saved {$savedMb} MB."
            . ($skipped > 0 ? " {$skipped} file(s) skipped (couldn't be read as images)." : ''));
    }
}
