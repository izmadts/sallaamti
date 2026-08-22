<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            // `age` stays as-is (browse/search filter on it directly) —
            // this is the real, verifiable source it now gets derived from.
            // Nullable because existing profiles predate this column; the
            // model's saving hook only recomputes `age` when it's set.
            $table->date('date_of_birth')->nullable()->after('age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nikah_profiles', function (Blueprint $table) {
            $table->dropColumn('date_of_birth');
        });
    }
};
