<?php

namespace Tests\Feature\User\Auth;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSuccessfulWithRightPassword()
    {
        $password = Str::random(12);

        $user = factory(User::class)
            ->create(['password' => $password]);

        $this->assertGuest();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password
        ])->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('users.show', $user));
    }

    public function testFailedWithWrongPassword()
    {
        $user = factory(User::class)
            ->create();

        $this->assertGuest();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => Str::random(12)
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => __('auth.failed')])
            ->assertSessionHas('email', $user->email);

        $this->assertGuest();
    }

    public function testAuthFailedSeeInfoRecoverPassword()
    {
        $user = factory(User::class)
            ->create();

        $response = $this->followingRedirects()
            ->post(route('login'), [
                'email' => $user->email,
                'password' => Str::random(12)
            ])
            ->assertOk()
            ->assertSeeText(__('auth.failed'))
            ->assertSeeText(__('Check whether you entered your mailbox and password correctly.'))
            ->assertSee(route('password.request', ['email' => $user->email]))
            ->assertSeeText(__('recover the password'));
    }
}
