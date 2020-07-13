<?php

namespace Tests\Feature\User\Invitation;

use App\Http\Controllers\UserInvitationController;
use App\Notifications\InvitationNotification;
use App\User;
use App\UserInvitation;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserInvitationCheckInvitationMethodTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testInvitationNotFound()
    {
        /*
        $invitation = factory(UserInvitation::class)
            ->create();
        */

        $controller = new UserInvitationController();

        $response = $controller->checkInvitation(request(), null);

        $this->assertEquals(route('users.invitation.create'), $response->getTargetUrl());
        $this->assertEquals(__('Invitation not found'), $response->getSession()->get('errors')->get('error')[0]);
    }

    public function testInvitationIsUsed()
    {
        $invitation = factory(UserInvitation::class)->create();
        $invitation->used();
        $invitation->save();

        $controller = new UserInvitationController();

        $response = $controller->checkInvitation(request(), $invitation);

        $this->assertEquals(route('users.invitation.create'), $response->getTargetUrl());
        $this->assertEquals(__('Registration has already taken place at this link'), $response->getSession()->get('errors')->get('error')[0]);
    }

    public function testUserWithEmailFound()
    {
        $user = factory(User::class)
            ->create();

        $invitation = factory(UserInvitation::class)
            ->create(['email' => $user->email]);

        $controller = new UserInvitationController();

        $response = $controller->checkInvitation(request(), $invitation);

        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertEquals(__('The user with this email address is already registered with the mailbox.').' '.
            __('Please log in or restore your password'), $response->getSession()->get('errors')->get('error')[0]);
    }
}
