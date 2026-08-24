<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    // Sensible starting defaults — admin-editable from day one, never
    // meant to stay hardcoded (see ensureSeeded() below). Renewal rate
    // stays flat 10% across tiers per the hiring document's own example;
    // first-purchase/verified rates step up modestly per tier so "seniors
    // can increase in percentage" per the user's explicit direction.
    private const TIER_MULTIPLIER = [
        'nikah_counselor' => 1.0,
        'certified_nikah_counselor' => 1.15,
        'senior_nikah_counselor' => 1.3,
        'regional_nikah_coordinator' => 1.5,
    ];

    protected $fillable = [
        'rule_type', 'nikah_package_id', 'tier', 'is_renewal',
        'rate_type', 'rate_value', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_renewal' => 'boolean',
            'is_active' => 'boolean',
            'rate_value' => 'decimal:2',
        ];
    }

    public function nikahPackage()
    {
        return $this->belongsTo(NikahPackage::class);
    }

    public function applyTo(float $baseAmount): float
    {
        return $this->rate_type === 'percentage'
            ? round($baseAmount * ((float) $this->rate_value / 100), 2)
            : (float) $this->rate_value;
    }

    public static function findFor(string $ruleType, ?int $nikahPackageId, string $tier, bool $isRenewal = false): ?self
    {
        return static::where('rule_type', $ruleType)
            ->where('nikah_package_id', $nikahPackageId)
            ->where('tier', $tier)
            ->where('is_renewal', $isRenewal)
            ->where('is_active', true)
            ->first();
    }

    // Idempotent, same spirit as PermissionCatalog::ensureSeeded() — fills
    // in any missing (rule_type, tier, is_renewal) combination with a
    // sensible default rather than requiring admin to hand-create every
    // row, but never overwrites a rate admin already configured.
    //
    // 'package' rules are deliberately package-agnostic (nikah_package_id
    // is always null here, same as 'verified_profile') — one slab per
    // tier applies to whichever package was actually purchased. Confirmed
    // explicitly by the user: every package had ended up with identical
    // per-tier rates anyway (nothing package-specific was ever actually
    // configured), so one universal slab removes duplicate rows without
    // losing any real customization.
    public static function ensureSeeded(): void
    {
        $tiers = array_keys(MatchmakerApplication::LEVELS);

        foreach ($tiers as $tier) {
            $multiplier = self::TIER_MULTIPLIER[$tier];

            static::firstOrCreate(
                ['rule_type' => 'verified_profile', 'nikah_package_id' => null, 'tier' => $tier, 'is_renewal' => false],
                ['rate_type' => 'fixed', 'rate_value' => round(200 * $multiplier)]
            );

            static::firstOrCreate(
                ['rule_type' => 'package', 'nikah_package_id' => null, 'tier' => $tier, 'is_renewal' => false],
                ['rate_type' => 'percentage', 'rate_value' => round(20 * $multiplier, 2)]
            );

            static::firstOrCreate(
                ['rule_type' => 'package', 'nikah_package_id' => null, 'tier' => $tier, 'is_renewal' => true],
                ['rate_type' => 'percentage', 'rate_value' => 10]
            );
        }
    }
}
