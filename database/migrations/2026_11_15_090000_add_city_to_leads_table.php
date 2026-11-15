<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Owner, 15 Nov 2026: city-wise lead assignment needs a real column to
// filter/sort/group by — today the city only sits inside free-text `notes`
// as "Shehar: <city>" (typed by hand when a walk-in/Facebook lead is
// entered), which can't be filtered or counted. Backfills every existing
// lead it can read a city out of; from here on, Lead::normalizeCity() is
// the one place "Karachi" vs "karachi" vs "Krachi, Pakistan" all become
// the same value, for both this backfill and the admin form going forward.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('city', 80)->nullable()->after('email')->index();
        });

        DB::table('leads')->whereNotNull('notes')->where('notes', 'like', '%Shehar%')->orderBy('id')
            ->chunkById(500, function ($leads) {
                foreach ($leads as $lead) {
                    if (!preg_match('/Shehar\s*:\s*([^|\n\r]+)/iu', (string) $lead->notes, $m)) {
                        continue;
                    }
                    $city = \App\Models\Lead::normalizeCity($m[1]);
                    if ($city) {
                        DB::table('leads')->where('id', $lead->id)->update(['city' => $city]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('city');
        });
    }
};
