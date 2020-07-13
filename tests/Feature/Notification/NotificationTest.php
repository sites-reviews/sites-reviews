<?php

namespace Tests\Feature\Notification;

use App\Notifications\TestNotification;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRoute()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('preview_notification'))
            ->assertOk()
            ->assertSeeText(config('app.name'))
            ->assertSeeText('© '.date('Y'))
            ->assertSeeText(__('Hello!'))
            ->assertSeeText(__('The introduction to the notification.'))
            ->assertSeeText(__('Notification Action'))
            ->assertSeeText(__('Thank you for using our application!'))
            ->assertSeeText(__('Regards'));
    }

    public function testToArray()
    {
        $user = factory(User::class)->create();

        $notification = new TestNotification();
        $array = $notification->toArray($user);

        $this->assertEquals(__('The title of the notification'), $array['title']);
        $this->assertEquals(__('The introduction to the notification.'), $array['description']);
        $this->assertEquals(url('/'), $array['url']);
    }
}
