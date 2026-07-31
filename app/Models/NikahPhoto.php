<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NikahPhoto extends Model
{
    protected $fillable = [
        'nikah_profile_id',
        'path',          // ← correct column name
        'is_primary',
        'order',
    ];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function nikahProfile()
    {
        return $this->belongsTo(NikahProfile::class);
    }
}
