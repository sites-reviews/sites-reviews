<?php

namespace Tests\Feature\Notification;

use App\CommentRating;
use App\Notifications\CommentWasLikedNotification;
use App\User;
use Tests\TestCase;

class CommentWasLikedNotificationTest extends TestCase
{
    public function testViaIsEmptyIfVoteNegative()
    {
        $user = factory(User::class)
            ->create();

        $rating = factory(CommentRating::class)
            ->create(['rating' => '-1']);

        $via = (new CommentWasLikedNotification($rating))->via($user);

        $this->assertEquals([], $via);
    }

    public function testViaIsEmptyIfUserDisableNotifcaionSettings()
    {
        $rating = factory(CommentRating::class)
            ->create(['rating' => '1']);

        $user = $rating->rateable->create_user;
        $user->notificationSetting->db_when_comment_was_liked = false;
        $user->push();

        $via = (new CommentWasLikedNotification($rating))->via($user);

        $this->assertEquals([], $via);
    }

    public function testVia()
    {
        $user = factory(User::class)
            ->create();

        $rating = factory(CommentRating::class)
            ->create();

        $via = (new CommentWasLikedNotification($rating))->via($user);

        $this->assertEquals(['database'], $via);
    }

    public function testToMail()
    {
        $user = factory(User::class)
            ->create();

        $rating = factory(CommentRating::class)
            ->create();

        $mail = (new CommentWasLikedNotification($rating))
            ->toMail($user);

        $this->assertEquals(__('Someone liked your comment'), $mail->subject);
        $this->assertEquals(__(':userName liked your comment', ['userName' => $rating->create_user->name]), $mail->introLines[0]);
        $this->assertEquals(__('Go to the comment'), $mail->actionText);
        $this->assertEquals($rating->rateable->getRedirectToUrl(), $mail->actionUrl);
    }

    public function testToArray()
    {
        $user = factory(User::class)
            ->create();

        $rating = factory(CommentRating::class)
            ->create();

        $array = (new CommentWasLikedNotification($rating))
            ->toArray($user);

        $this->assertEquals(__('Someone liked your comment'), $array['title']);
        $this->assertEquals(__(':userName liked your comment', ['userName' => $rating->create_user->name]), $array['description']);
        $this->assertEquals($rating->rateable->getRedirectToUrl(), $array['url']);
    }
}
