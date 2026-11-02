<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            // Null = not a poll, same "absence means off" convention as
            // media/social_targets on this table. A plain JSON array of
            // option strings — votes live in their own table below, keyed
            // by option index into this array.
            $table->json('poll_options')->nullable()->after('tags');
        });

        Schema::create('community_post_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('option_index');
            $table->timestamps();

            // One vote per member per poll — voting again changes it
            // (updateOrCreate), same as every social platform's own poll UX.
            $table->unique(['community_post_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_post_poll_votes');
        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropColumn('poll_options');
        });
    }
};
