<?php

namespace Tests\Feature\User\Auth\ResetPassword;

use App\Notifications\PasswordResetNotification;
use App\PasswordReset;
use App\User;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class RequestEmailTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRequestEmailRouteIsOk()
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertViewHas('email', null)
            ->assertSeeText(__('Send Password Reset Link'))
            ->assertDontSeeText(__('Instructions for password recovery have been sent to the specified mailbox'));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSendMailbox()
    {
        $email = $this->faker->email;

        $this->get(route('password.request', ['email' => $email]))
            ->assertOk()
            ->assertViewHas('email', $email)
            ->assertSee($email)
            ->assertSeeText(__('Send Password Reset Link'))
            ->assertDontSeeText(__('Instructions for password recovery have been sent to the specified mailbox'));
    }


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testResetLinkEmailIsOk()
    {
        Notification::fake();

        $user = factory(User::class)
            ->create();

        $this->post(route('password.email'), [
            'email' => $user->email
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('notification_send', true);

        $passwordReset = $user->passwordResets()->first();

        $this->assertNotNull($passwordReset);
        $this->assertNotNull($passwordReset->token);
        $this->assertEquals(64, mb_strlen($passwordReset->token));
        $this->assertEquals($user->email, $passwordReset->email);

        Notification::assertSentTo(
            $user,
            function (PasswordResetNotification $notification, $channels) use ($passwordReset) {
                return $notification->passwordReset->id === $passwordReset->id;
            }
        );
    }

    public function testSeeNotificationSendText()
    {
        $this->withSession(['notification_send' => true])
            ->get(route('password.request'))
            ->assertOk()
            ->assertSeeText(__('Instructions for password recovery have been sent to the specified mailbox'));
    }

    public function testSeeEmailNotFound()
    {
        $this->post(route('password.email'), [
            'email' => $this->faker->email
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHasErrors('error', 'The user with this mailbox was not found. Check whether you entered your mailbox correctly');
    }
}
