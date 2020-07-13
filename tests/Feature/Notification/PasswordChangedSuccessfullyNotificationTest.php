<?php

namespace Tests\Feature\Notification;

use App\Notifications\PasswordChangedSuccessfullyNotification;
use App\User;
use Tests\TestCase;

class PasswordChangedSuccessfullyNotificationTest extends TestCase
{
    public function testVia()
    {
        $user = factory(User::class)
            ->create();

        $via = (new PasswordChangedSuccessfullyNotification($user))->via($user);

        $this->assertEquals(['mail'], $via);
    }

    public function testMail()
    {
        $user = factory(User::class)
            ->create();

        $mail = (new PasswordChangedSuccessfullyNotification($user))
            ->toMail($user);

        $this->assertEquals(__('Password is changed'), $mail->subject);
        $this->assertEquals(__('Your account password was successfully changed'), $mail->introLines[0]);
        $this->assertEquals(__('Go to profile'), $mail->actionText);
        $this->assertEquals(route('users.show', ['user' => $user]), $mail->actionUrl);
    }
}
