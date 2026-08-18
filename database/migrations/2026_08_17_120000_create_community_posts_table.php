<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('photo')->nullable();
            $table->dateTime('event_at')->nullable();
            $table->json('tags')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Carry existing Activities over as tagged posts rather than losing
        // them — admin_id/author stays null (no author on the old data),
        // and timestamps are copied explicitly (not Model::create(), which
        // would stamp `now()` and wrongly bump legacy posts to the top of
        // the new merged feed).
        if (Schema::hasTable('activity_posts')) {
            $activities = DB::table('activity_posts')->get();
            $now = now();

            foreach ($activities as $activity) {
                DB::table('community_posts')->insert([
                    'author_id' => null,
                    'title' => $activity->title,
                    'body' => $activity->description,
                    'photo' => $activity->photo,
                    'event_at' => $activity->activity_date,
                    'tags' => json_encode(['Activity']),
                    'status' => $activity->is_active ? 'published' : 'draft',
                    'published_at' => $activity->is_active ? $activity->created_at : null,
                    'created_at' => $activity->created_at ?? $now,
                    'updated_at' => $activity->updated_at ?? $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('community_posts');
    }
};
