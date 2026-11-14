<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Digital Library, phase 2 (owner spec, 14 Nov 2026): tracking fields the
// member-app side (notifications, offline re-download, "New" badge, counts)
// needs on top of the web-only columns from create_library_items_table.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('library_items', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('is_published');
            // Set once the push for this item has actually gone out — guards
            // against double-notifying if an admin re-saves a published item.
            $table->timestamp('notified_at')->nullable()->after('published_at');
            // Bumped whenever the PDF file is replaced, so the app knows its
            // offline copy is stale and re-downloads instead of trusting a
            // same-named file that quietly changed underneath it.
            $table->unsignedSmallInteger('version')->default(1)->after('file_path');
            $table->unsignedInteger('file_size')->nullable()->after('version');
            $table->string('review_note')->nullable()->after('description');
            $table->unsignedInteger('view_count')->default(0)->after('download_count');
            $table->unsignedInteger('share_count')->default(0)->after('view_count');
        });
    }

    public function down(): void
    {
        Schema::table('library_items', function (Blueprint $table) {
            $table->dropColumn(['published_at', 'notified_at', 'version', 'file_size', 'review_note', 'view_count', 'share_count']);
        });
    }
};
