<?php

namespace Tests\Feature\User\Auth\ResetPassword;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\PasswordReset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SetNewPasswordTest extends TestCase
{
    public function testSetNewPasswordFormIsOk()
    {
        $passwordReset = factory(PasswordReset::class)
            ->create();

        $this->get(route('password.reset', ['token' => $passwordReset->token]))
            ->assertOk()
            ->assertSeeText(__("Reset Password"));
    }

    public function testResetPasswordIsOk()
    {
        $passwordReset = factory(PasswordReset::class)
            ->create();

        $newPassword = rand(1, 9).''.$this->faker->password;

        $user = $passwordReset->user;

        $oldPasswordHash = $user->password;

        Event::fake();

        $this->post(route('password.update', [
            'token' => $passwordReset->token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.show', $user));

        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $passwordReset->refresh();

        $this->assertNotNull($passwordReset->used_at);

        $this->assertNotEquals($oldPasswordHash, $user->password);

        Event::assertDispatched(\Illuminate\Auth\Events\PasswordReset::class);
    }

    public function testCheckPasswordResetLinkIncorrectError()
    {
        $controller = new ResetPasswordController();

        $response = $controller->checkPasswordReset(request(), null);

        $this->assertNotNull($response);
        $this->assertEquals(route('home'), $response->getTargetUrl());
        $this->assertEquals(__('The password recovery link is incorrect'), $response->getSession()->get('errors')->get('error')[0]);
    }

    public function testCheckPasswordResetLinkIsUsedError()
    {
        $passwordReset = factory(PasswordReset::class)
            ->create();
        $passwordReset->used_at = now();
        $passwordReset->save();

        $controller = new ResetPasswordController();

        $response = $controller->checkPasswordReset(request(), $passwordReset);

        $this->assertNotNull($response);
        $this->assertEquals(route('home'), $response->getTargetUrl());
        $this->assertEquals(__('The link was already used for password recovery'), $response->getSession()->get('errors')->get('error')[0]);
    }

    public function testPasswordResetExpiredError()
    {
        $passwordReset = factory(PasswordReset::class)->create();

        Carbon::setTestNow(now()->addMinutes(config('auth.passwords.users.expire'))->addMinute());

        $controller = new ResetPasswordController();

        $response = $controller->checkPasswordReset(request(), $passwordReset);

        $this->assertNotNull($response);
        $this->assertEquals(route('password.request'), $response->getTargetUrl());
        $this->assertEquals(__('The link to restore is outdated. You can send a new link.'), $response->getSession()->get('errors')->get('error')[0]);
    }

    public function testPasswordResetExpiredErrorDontShowIfNotExpired()
    {
        $passwordReset = factory(PasswordReset::class)->create();

        Carbon::setTestNow(now()->addMinutes(config('auth.passwords.users.expire'))->subMinute());

        $controller = new ResetPasswordController();

        $response = $controller->checkPasswordReset(request(), $passwordReset);

        $this->assertNull($response);
    }

}
