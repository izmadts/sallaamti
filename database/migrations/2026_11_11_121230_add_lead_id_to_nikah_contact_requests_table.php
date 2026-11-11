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
        Schema::table('nikah_contact_requests', function (Blueprint $table) {
            // Nullable — a matchmaker can request contact while just
            // browsing (no client context), not only from a specific
            // client's pick-mode flow. When set, lets admin see which of
            // the counselor's clients this request was actually for.
            $table->foreignId('lead_id')->nullable()->after('nikah_profile_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nikah_contact_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
        });
    }
};
