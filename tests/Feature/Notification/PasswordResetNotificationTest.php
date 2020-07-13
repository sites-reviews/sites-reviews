<?php

namespace Tests\Feature\Notification;

use App\Notifications\PasswordResetNotification;
use App\PasswordReset;
use App\User;
use Tests\TestCase;

class PasswordResetNotificationTest extends TestCase
{
    public function testVia()
    {
        $user = factory(User::class)
            ->create();

        $passwordReset = factory(PasswordReset::class)
            ->create(['email' => $user->email]);

        $via = (new PasswordResetNotification($passwordReset))->via($user);

        $this->assertEquals(['mail'], $via);
    }

    public function testMail()
    {
        $user = factory(User::class)
            ->create();

        $passwordReset = factory(PasswordReset::class)
            ->create(['email' => $user->email]);

        $mail = (new PasswordResetNotification($passwordReset))
            ->toMail($user);

        $this->assertEquals(__('Password recovery'), $mail->subject);
        $this->assertEquals(__('To set a new password click the button below'), $mail->introLines[0]);
        $this->assertEquals(__('Set a new password'), $mail->actionText);
        $this->assertEquals(route('password.reset', ['token' => $passwordReset->token]), $mail->actionUrl);
    }
}
