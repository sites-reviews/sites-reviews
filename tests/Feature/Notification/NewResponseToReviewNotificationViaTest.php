<?php

namespace Tests\Feature\Notification;

use App\Notifications\NewResponseToReviewNotification;
use App\Comment;
use App\User;
use Tests\TestCase;

class NewResponseToReviewNotificationViaTest extends TestCase
{
    public function testEveryFalse()
    {
        $user = factory(User::class)->create();
        $user->notificationSetting->email_response_to_my_review = false;
        $user->notificationSetting->db_response_to_my_review = false;
        $user->push();

        $via = (new NewResponseToReviewNotification(new Comment))->via($user);

        $this->assertEquals([], $via);
    }

    public function testMailTrue()
    {
        $user = factory(User::class)->create();
        $user->notificationSetting->email_response_to_my_review = true;
        $user->push();

        $via = (new NewResponseToReviewNotification(new Comment))->via($user);

        $this->assertContains('mail', $via);
    }

    public function testMailFalse()
    {
        $user = factory(User::class)->create();
        $user->notificationSetting->email_response_to_my_review = false;
        $user->push();

        $via = (new NewResponseToReviewNotification(new Comment))->via($user);

        $this->assertNotContains('mail', $via);
    }

    public function testDatabaseTrue()
    {
        $user = factory(User::class)->create();
        $user->notificationSetting->db_response_to_my_review = true;
        $user->push();

        $via = (new NewResponseToReviewNotification(new Comment))->via($user);

        $this->assertContains('database', $via);
    }

    public function testDatabaseFalse()
    {
        $user = factory(User::class)->create();
        $user->notificationSetting->db_response_to_my_review = false;
        $user->push();

        $via = (new NewResponseToReviewNotification(new Comment))->via($user);

        $this->assertNotContains('database', $via);
    }
}
