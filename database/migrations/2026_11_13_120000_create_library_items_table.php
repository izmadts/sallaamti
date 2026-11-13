<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Sallaamti Library (owner, 13 Nov 2026): free Urdu booklets and Sallaamti's
// own documents (Manhaj, Manshoor) that visitors read, download and share.
// Uploaded from admin → Library; only published items show on /library.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();          // e.g. BK1 (ties to the ulama review sheet)
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category', 30)->default('family'); // see LibraryItem::CATEGORIES
            $table->text('description')->nullable();
            $table->string('file_path');                     // PDF on the public disk
            $table->string('cover_path')->nullable();
            $table->unsignedSmallInteger('pages')->nullable();
            $table->string('language', 10)->default('ur');
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_items');
    }
};
