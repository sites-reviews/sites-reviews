<?php

namespace Tests\Feature\User\Invitation;

use App\Notifications\InvitationNotification;
use App\Review;
use App\Site;
use App\User;
use App\UserInvitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserInvitationCreateUserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateUserRouteIsOk()
    {
        $invitation = factory(UserInvitation::class)->create();

        $this->get(route('users.invitation.create.user', ['token' => $invitation->token]))
            ->assertOk()
            ->assertSee(route('users.invitation.store.user', ['token' => $invitation->token]));
    }

    public function testStoreUserRouteIsOk()
    {
        $invitation = factory(UserInvitation::class)
            ->create();

        $userNew = factory(User::class)
            ->make();

        Event::fake();

        $this->assertGuest();

        $response = $this->post(route('users.invitation.store.user', ['token' => $invitation->token]), [
            'name' => $userNew->name,
            'gender' => $userNew->gender,
            'password' => $userNew->password,
            'password_confirmation' => $userNew->password,
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas(['success' => __('You have successfully registered')]);

        $user = User::where('email', $invitation->email)->first();

        $response->assertRedirect(route('users.show', $user));

        $this->assertNotNull($user);
        $this->assertEquals($userNew->name, $user->name);
        $this->assertEquals($invitation->email, $user->email);
        $this->assertNotNull($user->email_verified_at);

        $this->assertAuthenticatedAs($user);

        Event::assertDispatched(function (Registered $event) use ($user) {
            return $event->user->id === $user->id;
        });
    }
}
