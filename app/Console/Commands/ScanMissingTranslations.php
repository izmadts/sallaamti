<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ScanMissingTranslations extends Command
{
    protected $signature = 'translations:scan {path? : Subdirectory under resources/views to scan}';

    protected $description = 'List Blade files under resources/views that still contain plain-text lines not wrapped in __(\'db....\') — a dev aid for the i18n sweep, not shipped functionality';

    public function handle(): void
    {
        $base = resource_path('views' . ($this->argument('path') ? '/' . trim($this->argument('path'), '/') : ''));

        $files = File::allFiles($base);
        $flagged = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php' || !str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $lines = file($file->getPathname());
            $suspects = [];

            foreach ($lines as $i => $line) {
                // A line that has visible text between tags but no __('db....') on it,
                // and isn't a Blade directive/comment/script/style line, is worth a look.
                $trimmed = trim($line);

                if ($trimmed === '' || str_starts_with($trimmed, '{{--') || str_starts_with($trimmed, '@') || str_starts_with($trimmed, '<!--')) {
                    continue;
                }

                if (str_contains($line, "__('db.") || str_contains($line, '__("db.')) {
                    continue;
                }

                if (preg_match('/>([A-Za-z][A-Za-z0-9 ,.!?\'"()&:\-]{2,})</', $line)) {
                    $suspects[] = $i + 1;
                }
            }

            if (!empty($suspects)) {
                $flagged[str_replace(resource_path('views') . DIRECTORY_SEPARATOR, '', $file->getPathname())] = count($suspects);
            }
        }

        if (empty($flagged)) {
            $this->info('No obviously-untranslated lines found.');
            return;
        }

        arsort($flagged);

        $this->table(['View', 'Untranslated-looking lines'], collect($flagged)->map(fn ($count, $view) => [$view, $count])->toArray());
        $this->comment(count($flagged) . ' file(s) flagged — this is a heuristic, not authoritative. Check each manually.');
    }
}
