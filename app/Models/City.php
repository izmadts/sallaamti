<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Reference data only (no relationships) — a flat, searchable list backing
// the Nikah profile wizard's City field. See the create_cities_table
// migration for why this is a real indexed table rather than the bundled-
// JSON-into-a-cached-array pattern app/Support/CountryStates.php uses for
// Country/State: at ~150k rows, preloading all of it isn't practical, so
// this is queried live instead (see Api\V1\MetaController::cities()).
class City extends Model
{
    protected $fillable = ['name', 'state', 'country'];
}
