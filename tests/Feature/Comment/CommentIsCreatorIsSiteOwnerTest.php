<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\Review;
use App\Site;
use App\SiteOwner;
use Tests\TestCase;

class CommentIsCreatorIsSiteOwnerTest extends TestCase
{
    public function testTrue()
    {
        $site = factory(Site::class)
            ->states('with_owner')
            ->create();

        $user = $site->userOwner;

        $review = factory(Review::class)
            ->create([
                'site_id' => $site->id
            ]);

        $comment = factory(Comment::class)
            ->create([
                'review_id' => $review->id,
                'create_user_id' => $user->id
            ]);

        $this->assertTrue($comment->isCreatorIsSiteOwner());
    }

    public function testFalseIfNotConfirmed()
    {
        $site = factory(Site::class)
            ->states('with_owner')
            ->create();

        $user = $site->userOwner;

        $review = factory(Review::class)
            ->create([
                'site_id' => $site->id,
                'create_user_id' => $user->id
            ]);

        $comment = factory(Comment::class)
            ->create([
                'review_id' => $review->id
            ]);

        $this->assertFalse($comment->isCreatorIsSiteOwner());
    }
}
