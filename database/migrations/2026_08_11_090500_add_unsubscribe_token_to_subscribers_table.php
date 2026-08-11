<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->string('unsubscribe_token')->nullable()->unique()->after('verification_token');
        });

        // Backfill existing rows so unsubscribe-by-token works for subscribers
        // who signed up before this column existed.
        DB::table('subscribers')->whereNull('unsubscribe_token')->orderBy('id')->get()->each(function ($row) {
            DB::table('subscribers')->where('id', $row->id)->update([
                'unsubscribe_token' => Str::random(64),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn('unsubscribe_token');
        });
    }
};
