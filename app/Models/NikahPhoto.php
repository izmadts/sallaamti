<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NikahPhoto extends Model
{
    protected $fillable = ['nikah_profile_id', 'path', 'is_primary', 'order'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function profile()
    {
        return $this->belongsTo(NikahProfile::class, 'nikah_profile_id');
    }
    public function photos()
    {
        return $this->hasMany(NikahPhoto::class)->orderBy('order');
    }

    public function primaryPhoto(): ?NikahPhoto
    {
        return $this->photos()->where('is_primary', true)->first()
            ?? $this->photos()->first();
    }
}
