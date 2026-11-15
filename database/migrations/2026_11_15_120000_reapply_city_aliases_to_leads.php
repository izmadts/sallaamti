<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// SEO Agent quick-fix #4 (15 Nov 2026): the original backfill
// (2026_11_15_090000_add_city_to_leads_table.php) ran before karchi/khi/
// lhr/rwp/fsd were added to Lead::CITY_ALIASES, so a lead whose notes said
// e.g. "Shehar: karchi" got normalizeCity()'s title-case fallback — "Karchi"
// (typo preserved) — instead of today's correct "Karachi". Re-runs the same
// regex-from-notes extraction for every lead with "Shehar" in notes and
// overwrites city only where the freshly-normalized value actually differs,
// so a lead a staff member has since hand-corrected isn't clobbered.
return new class extends Migration
{
    public function up(): void
    {
        DB::table('leads')->whereNotNull('notes')->where('notes', 'like', '%Shehar%')->orderBy('id')
            ->chunkById(500, function ($leads) {
                foreach ($leads as $lead) {
                    if (!preg_match('/Shehar\s*:\s*([^|\n\r]+)/iu', (string) $lead->notes, $m)) {
                        continue;
                    }
                    $city = \App\Models\Lead::normalizeCity($m[1]);
                    if ($city && $city !== $lead->city) {
                        DB::table('leads')->where('id', $lead->id)->update(['city' => $city]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Not reversible — the previous (wrong) values aren't recorded
        // anywhere to restore. Same as the original backfill migration.
    }
};
