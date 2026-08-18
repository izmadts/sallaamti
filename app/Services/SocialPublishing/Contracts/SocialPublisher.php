<?php

namespace App\Services\SocialPublishing\Contracts;

use App\Models\CommunityPost;
use App\Models\SocialAccount;

interface SocialPublisher
{
    /**
     * Publish a CommunityPost to the given connected account.
     *
     * @return array{success: bool, external_id: ?string, error: ?string}
     */
    public function publish(CommunityPost $post, SocialAccount $account): array;
}
