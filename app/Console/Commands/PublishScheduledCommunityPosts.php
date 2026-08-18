<?php

namespace App\Console\Commands;

use App\Models\CommunityPost;
use App\Models\Setting;
use App\Services\CommunityPostPublisher;
use Illuminate\Console\Command;

class PublishScheduledCommunityPosts extends Command
{
    protected $signature = 'wall:publish-scheduled';

    protected $description = 'Publish the next batch of queued Community Posts (drains the admin Queue by queue_position order).';

    public function handle(CommunityPostPublisher $publisher): int
    {
        $batchSize = (int) Setting::get('scheduled_batch_size', 3);

        if ($batchSize <= 0) {
            $this->info('Scheduled batch size is 0 — nothing to do.');
            return self::SUCCESS;
        }

        $posts = CommunityPost::scheduled()->take($batchSize)->get();

        foreach ($posts as $post) {
            $publisher->publish($post);
            $this->info("Published #{$post->id}: {$post->title}");
        }

        if ($posts->isEmpty()) {
            $this->info('Queue is empty — nothing to publish.');
        }

        return self::SUCCESS;
    }
}
