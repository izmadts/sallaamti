<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// The admin-managed catalog of matchmaking packages (Verified / Assisted /
// Premium / VIP, or whatever an admin renames/adds/retires later — nothing
// about the tiers themselves is hardcoded). A Lead references one via
// nikah_package_id; the package's own duration_days/proposal_limit drive
// expiry and the proposal-sending cap in Matchmaker\ClientController.
class NikahPackage extends Model
{
    protected $fillable = [
        'name', 'slug', 'tagline', 'price', 'currency', 'duration_days', 'proposal_limit',
        'consultant_level', 'description', 'features', 'color', 'icon', 'sort_order',
        'is_active', 'show_on_public_page',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
            'show_on_public_page' => 'boolean',
        ];
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function isOneTime(): bool
    {
        return $this->duration_days === null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_active', true)->where('show_on_public_page', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }
}
