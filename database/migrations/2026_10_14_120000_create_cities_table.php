<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Name-keyed, not ID/FK-based — matches the existing country/state
        // convention (nikah_profiles.country/state are plain strings, and
        // app/Support/CountryStates.php is a name => [names] map with no
        // Country/State models at all). ~150k rows from
        // resources/data/cities.json via `php artisan cities:import`, too
        // large to preload like the states list, so this is a real indexed
        // table queried through a live-search endpoint instead.
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            // Lengths kept short on purpose: the composite index below sums
            // all three columns' byte length, and production MySQL caps a
            // single index at 1000 bytes. With utf8mb4 (4 bytes/char),
            // 60+85+100 = 245 chars -> 980 bytes, just under the cap.
            $table->string('name', 100);
            $table->string('state', 85)->nullable();
            $table->string('country', 60);
            $table->timestamps();

            $table->index(['country', 'state', 'name']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
