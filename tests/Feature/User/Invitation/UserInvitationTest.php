<?php

namespace Tests\Feature\User\Invitation;

use App\Notifications\InvitationNotification;
use App\Review;
use App\Site;
use App\User;
use App\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserInvitationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateInvitationRouteIsOk()
    {
        $this->get(route('users.invitation.create'))
            ->assertOk();
    }

    public function testStoreInvitationRouteIsOk()
    {
        Notification::fake();

        $invitationNew = factory(UserInvitation::class)
            ->make();

        $this->post(route('users.invitation.store'), [
            'email' => $invitationNew->email
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.invitation.create'))
            ->assertSessionHas(
                [
                    'invitation_was_sent' => true,
                    'email' => $invitationNew->email
                ]
            );

        $invitation = UserInvitation::where('email', $invitationNew->email)->first();

        $this->assertNotNull($invitation);
        $this->assertEquals($invitationNew->email, $invitation->email);
        $this->assertNotNull($invitation->token);
        $this->assertEquals(64, mb_strlen($invitation->token));
        $this->assertNull($invitation->used_at);

        $this->get(route('users.invitation.create'))
            ->assertOk()
            ->assertSeeText(__('The email was successfully sent to your mailbox :email', ['email' => $invitation->email]))
            ->assertSeeText(__('Now open your mailbox :email and click on the button in the email', ['email' => $invitation->email]));

        Notification::assertSentTo(
            new AnonymousNotifiable,
            InvitationNotification::class,
            function ($notification, $channels, $notifiable) use ($invitation) {

                return $notification->invitation->id === $invitation->id;
            }
        );
    }

    public function testStoreIfEmailAlreadyRegistered()
    {
        $user = factory(User::class)
            ->create();

        $this->post(route('users.invitation.store'), [
            'email' => $user->email
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(
                [
                    'error' => __('The user with this email address is already registered with the mailbox.').' '.
                        __('Please log in or restore your password')
                ]
            );
    }
}
