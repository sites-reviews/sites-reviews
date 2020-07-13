<?php

namespace Tests\Feature\User\Auth;

use App\User;
use Tests\TestCase;

class UserLogoutTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSuccessfulLogout()
    {
        $user = factory(User::class)
            ->create();

        $this->be($user);

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertGuest();
    }
}
