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
        'name', 'name_ur', 'slug', 'tagline', 'tagline_ur', 'price', 'currency', 'duration_days', 'proposal_limit',
        'consultant_level', 'description', 'description_ur', 'features', 'features_ur', 'color', 'icon', 'sort_order',
        'is_active', 'show_on_public_page',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'features_ur' => 'array',
            'is_active' => 'boolean',
            'show_on_public_page' => 'boolean',
        ];
    }

    // Urdu columns are optional per-package overrides — fall back to the English
    // content whenever an admin hasn't filled in a translation yet, so the public
    // page never shows blank text just because the locale is 'ur'.
    public function localizedName(): string
    {
        return app()->getLocale() === 'ur' && $this->name_ur ? $this->name_ur : $this->name;
    }

    public function localizedTagline(): ?string
    {
        return app()->getLocale() === 'ur' && $this->tagline_ur ? $this->tagline_ur : $this->tagline;
    }

    public function localizedDescription(): ?string
    {
        return app()->getLocale() === 'ur' && $this->description_ur ? $this->description_ur : $this->description;
    }

    public function localizedFeatures(): array
    {
        return app()->getLocale() === 'ur' && !empty($this->features_ur) ? $this->features_ur : ($this->features ?? []);
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
