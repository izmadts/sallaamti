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
        // Ordered list of [{type: 'image'|'video', path: '...'}], for an
        // Instagram/Facebook-style multi-photo/video carousel. The existing
        // single photo/video (community_posts) and image (dua_requests)
        // columns stay in place and keep mirroring the first item — see
        // CommunityPost::setMediaAttribute()/DuaRequest::setMediaAttribute()
        // — so every pre-existing reader of ->photo/->video/->image keeps
        // working unchanged and only shows the cover item.
        Schema::table('community_posts', function (Blueprint $table) {
            $table->json('media')->nullable()->after('video');
        });

        Schema::table('dua_requests', function (Blueprint $table) {
            $table->json('media')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropColumn('media');
        });

        Schema::table('dua_requests', function (Blueprint $table) {
            $table->dropColumn('media');
        });
    }
};
