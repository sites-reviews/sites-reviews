<?php

namespace Tests\Feature\Notification;

use App\Notifications\ReviewWasLikedNotification;
use App\ReviewRating;
use Tests\TestCase;

class ReviewWasLikedNotificationViaTest extends TestCase
{
    public function testDatabaseTrue()
    {
        $rating = factory(ReviewRating::class)
            ->states('up')
            ->create();

        $user = $rating->rateable->create_user;
        $user->notificationSetting->db_when_review_was_liked = true;
        $user->push();

        $via = (new ReviewWasLikedNotification($rating))->via($user);

        $this->assertEquals(['database'], $via);
    }

    public function testDatabaseFalse()
    {
        $rating = factory(ReviewRating::class)
            ->states('up')
            ->create();

        $user = $rating->rateable->create_user;
        $user->notificationSetting->db_when_review_was_liked = false;
        $user->push();

        $via = (new ReviewWasLikedNotification(new ReviewRating))->via($user);

        $this->assertNotContains([], $via);
    }

    public function testSentIfVoteUp()
    {
        $rating = factory(ReviewRating::class)
            ->states('up')
            ->create();

        $user = $rating->rateable->create_user;
        $user->notificationSetting->db_when_review_was_liked = true;
        $user->push();

        $via = (new ReviewWasLikedNotification($rating))
            ->via($user);

        $this->assertEquals(['database'], $via);
    }

    public function testDontSentIfVoteDown()
    {
        $rating = factory(ReviewRating::class)
            ->states('down')
            ->create();

        $user = $rating->rateable->create_user;
        $user->notificationSetting->db_when_review_was_liked = true;
        $user->push();

        $via = (new ReviewWasLikedNotification($rating))
            ->via($user);

        $this->assertEquals([], $via);
    }
}
