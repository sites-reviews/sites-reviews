<?php

namespace Tests\Feature\Notification;

use App\Notifications\NewResponseToYourCommentNotification;
use App\Comment;
use App\User;
use Tests\TestCase;

class NewResponseToYourCommentNotificationViaTest extends TestCase
{
    public function testEveryFalse()
    {
        $user = factory(User::class)->create();
        $user->notificationSetting->email_response_to_my_comment = false;
        $user->notificationSetting->db_response_to_my_comment = false;
        $user->push();

        $via = (new NewResponseToYourCommentNotification(new Comment))->via($user);

        $this->assertEquals([], $via);
    }

    public function testMailTrue()
    {
        $user = factory(User::class)->create();
        $user->notificationSetting->email_response_to_my_comment = true;
        $user->push();

        $via = (new NewResponseToYourCommentNotification(new Comment))->via($user);

        $this->assertContains('mail', $via);
    }

    public function testMailFalse()
    {
        $user = factory(User::class)->create();
        $user->notificationSetting->email_response_to_my_comment = false;
        $user->push();

        $via = (new NewResponseToYourCommentNotification(new Comment))->via($user);

        $this->assertNotContains('mail', $via);
    }

    public function testDatabaseTrue()
    {
        $user = factory(User::class)->create();
        $user->notificationSetting->db_response_to_my_comment = true;
        $user->push();

        $via = (new NewResponseToYourCommentNotification(new Comment))->via($user);

        $this->assertContains('database', $via);
    }

    public function testDatabaseFalse()
    {
        $user = factory(User::class)->create();
        $user->notificationSetting->db_response_to_my_comment = false;
        $user->push();

        $via = (new NewResponseToYourCommentNotification(new Comment))->via($user);

        $this->assertNotContains('database', $via);
    }
}
