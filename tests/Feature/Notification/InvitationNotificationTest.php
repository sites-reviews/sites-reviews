<?php

namespace Tests\Feature\Notification;

use App\Notifications\InvitationNotification;
use App\UserInvitation;
use Illuminate\Notifications\AnonymousNotifiable;
use Tests\TestCase;

class InvitationNotificationTest extends TestCase
{
    public function testVia()
    {
        $invitation = factory(UserInvitation::class)
            ->create();

        $notifiable = new AnonymousNotifiable();

        $via = (new InvitationNotification($invitation))->via($notifiable);

        $this->assertEquals(['mail'], $via);
    }

    public function testMail()
    {
        $invitation = factory(UserInvitation::class)
            ->create();

        $notifiable = new AnonymousNotifiable();

        $mail = (new InvitationNotification($invitation))
            ->toMail($notifiable);

        $this->assertEquals(__("We invite you to register"), $mail->subject);
        $this->assertEquals(__("To continue registering, please click on the button below"), $mail->introLines[0]);
        $this->assertEquals(__("Сontinue registration"), $mail->actionText);
        $this->assertEquals(route('users.invitation.create.user', ['token' => $invitation->token]), $mail->actionUrl);
    }
}
