<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tracks the OUT-OF-SYSTEM marketing step: an admin/Nikah Counselor
        // posts a profile's shareSummary() card to their own WhatsApp
        // Channel/Facebook page to attract outside interest. There's no way
        // today to know which profiles have been marketed, where, or how
        // long ago — this is purely a log, it doesn't gate or change
        // anything else about the profile.
        Schema::create('nikah_profile_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nikah_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel'); // 'whatsapp_channel', 'facebook', 'other'
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['nikah_profile_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nikah_profile_shares');
    }
};
