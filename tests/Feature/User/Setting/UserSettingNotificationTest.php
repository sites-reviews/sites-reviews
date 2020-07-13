<?php

namespace Tests\Feature\User\Setting;

use App\Review;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserSettingNotificationTest extends TestCase
{
    public function testDefault()
    {
        $user = factory(User::class)
            ->create();

        $this->assertEquals(true, $user->notificationSetting->email_response_to_my_review);
        $this->assertEquals(true, $user->notificationSetting->db_response_to_my_review);
        $this->assertEquals(true, $user->notificationSetting->db_when_review_was_liked);
        $this->assertEquals(true, $user->notificationSetting->email_response_to_my_comment);
        $this->assertEquals(true, $user->notificationSetting->db_response_to_my_comment);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRouteIsOk()
    {
        $user = factory(User::class)
            ->create();

        $this->actingAs($user)
            ->get(route('users.settings.notifications', $user))
            ->assertOk()
            ->assertSeeText(__('Notifications to your email address'))
            ->assertSeeText(__('Notice on the website'));
    }

    public function testUpdateIsOk()
    {
        $user = factory(User::class)
            ->create();

        $this->actingAs($user)
            ->post(route('users.settings.notifications.update', $user), [
                'email_response_to_my_review' => false,
                'db_response_to_my_review' => true,
                'db_when_review_was_liked' => false,
                'email_response_to_my_comment' => true,
                'db_response_to_my_comment' => false,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.settings.notifications', $user))
            ->assertSessionHas('success', __('Notification settings have been changed successfully'));

        $user->refresh();

        $this->assertEquals(false, $user->notificationSetting->email_response_to_my_review);
        $this->assertEquals(true, $user->notificationSetting->db_response_to_my_review);
        $this->assertEquals(false, $user->notificationSetting->db_when_review_was_liked);
        $this->assertEquals(true, $user->notificationSetting->email_response_to_my_comment);
        $this->assertEquals(false, $user->notificationSetting->db_response_to_my_comment);


        $this->actingAs($user)
            ->followingRedirects()
            ->post(route('users.settings.notifications.update', $user), [
                'email_response_to_my_review' => false,
                'db_response_to_my_review' => true,
                'db_when_review_was_liked' => false,
                'email_response_to_my_comment' => true,
                'db_response_to_my_comment' => false,
            ])
            ->assertOk()
            ->assertSeeText(__('Notification settings have been changed successfully'));
    }
}
