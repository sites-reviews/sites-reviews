<?php

namespace Tests\Feature\User\Notification;

use App\Notifications\WelcomeNotification;
use App\Review;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserNotificationPolicyTest extends TestCase
{
    public function testCanSeeIfOwner()
    {
        $user = factory(User::class)
            ->create();

        $this->assertTrue($user->can('see_notifications', $user));
    }

    public function testCantSeeIfOtherUser()
    {
        $user = factory(User::class)
            ->create();

        $user2 = factory(User::class)
            ->create();

        $this->assertFalse($user->can('see_notifications', $user2));
    }
}
