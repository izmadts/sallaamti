<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            // Admin-only curation, so this lives on CommunityPost, not
            // DuaRequest — pinning a member's dua wouldn't fit the
            // "announcement" use case pinning is for.
            $table->boolean('is_pinned')->default(false)->after('status');
            $table->timestamp('pinned_at')->nullable()->after('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropColumn(['is_pinned', 'pinned_at']);
        });
    }
};
