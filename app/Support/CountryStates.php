<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

// Backed by resources/data/countries-states.json (from
// dr5hn/countries-states-cities-database, states-only — no cities, to keep
// this small: ~150KB, 250 countries, ~5,300 states/provinces). A static
// bundled file rather than a package/DB seed so there's nothing extra to
// install or migrate on deploy — it just ships with the rest of the code.
class CountryStates
{
    // [country_name => [state_name, ...]], keyed for O(1) lookup by country.
    public static function map(): array
    {
        return Cache::rememberForever('country_states_map', function () {
            $countries = json_decode(file_get_contents(resource_path('data/countries-states.json')), true) ?? [];

            $map = [];
            foreach ($countries as $country) {
                $map[$country['name']] = $country['states'];
            }

            return $map;
        });
    }

    /** @return array<int, string> every country name, alphabetical */
    public static function countries(): array
    {
        $names = array_keys(self::map());
        sort($names);

        return $names;
    }

    /** @return array<int, string> states/provinces for one country, or [] if unknown/none listed */
    public static function statesFor(?string $country): array
    {
        return self::map()[$country] ?? [];
    }
}
