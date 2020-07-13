<?php

namespace Tests\Feature\User\Notification;

use App\Notifications\WelcomeNotification;
use App\Review;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserNotificationTest extends TestCase
{
    public function testNoNotifications()
    {
        $user = factory(User::class)
            ->create();

        $this->actingAs($user)
            ->get(route('users.notifications.dropdown', ['user' => $user]))
            ->assertOk()
            ->assertViewHas('user', $user)
            ->assertViewIs('user.notification.dropdown')
            ->assertSeeText(__('There are no new notifications yet'));
    }

    public function testWelcomeNotification()
    {
        $user = factory(User::class)
            ->create();

        $user->notify(new WelcomeNotification($user));

        $response = $this->actingAs($user)
            ->get(route('users.notifications.dropdown', ['user' => $user]))
            ->assertOk()
            ->assertViewHas('user', $user)
            ->assertViewHas('unreadNotifications')
            ->assertViewIs('user.notification.dropdown')
            ->assertSeeText(__('Thank you for registering'));

        $this->assertNull($response->original->gatherData()['unreadNotifications']->first()->read_at);
    }

    public function testMarkAsRead()
    {
        $user = factory(User::class)
            ->create();

        $user->notify(new WelcomeNotification($user));

        $this->assertEquals(1, $user->unreadNotifications->count());

        $response = $this->actingAs($user)
            ->get(route('users.notifications.dropdown', ['user' => $user]))
            ->assertOk();

        $user->refresh();

        $this->assertEquals(0, $user->unreadNotifications->count());
    }

    public function testNotificationIndex()
    {
        $user = factory(User::class)
            ->create();

        $user->notify(new WelcomeNotification($user));

        $response = $this->actingAs($user)
            ->get(route('users.notifications', ['user' => $user]))
            ->assertOk();
    }
}
