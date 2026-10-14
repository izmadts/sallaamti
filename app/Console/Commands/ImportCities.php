<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// One-time (or re-run-on-deploy, it's idempotent via truncate) import of
// resources/data/cities.json into the cities table — see that file and
// the create_cities_table migration for where the data comes from and why
// it's a real table instead of a bundled-JSON-into-memory map.
class ImportCities extends Command
{
    protected $signature = 'cities:import';

    protected $description = 'Import resources/data/cities.json into the cities table';

    public function handle(): int
    {
        $path = resource_path('data/cities.json');
        if (!file_exists($path)) {
            $this->error("Missing {$path}");
            return self::FAILURE;
        }

        $cities = json_decode(file_get_contents($path), true);
        if ($cities === null) {
            $this->error('Failed to parse cities.json: ' . json_last_error_msg());
            return self::FAILURE;
        }

        $this->info('Importing ' . count($cities) . ' cities...');

        DB::table('cities')->truncate();

        $now = now();
        collect($cities)
            ->map(fn ($c) => [
                'name' => $c['name'],
                'state' => $c['state'],
                'country' => $c['country'],
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->chunk(1000)
            ->each(function ($chunk) {
                DB::table('cities')->insert($chunk->all());
            });

        $this->info('Done — ' . DB::table('cities')->count() . ' rows in cities table.');

        return self::SUCCESS;
    }
}
