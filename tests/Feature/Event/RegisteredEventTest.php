<?php

namespace Tests\Feature\Event;

use App\Notifications\InvitationNotification;
use App\Notifications\NewResponseToReviewNotification;
use App\Notifications\PasswordChangedSuccessfullyNotification;
use App\Notifications\ReviewWasLikedNotification;
use App\Comment;
use App\Notifications\WelcomeNotification;
use App\ReviewRating;
use App\User;
use App\UserInvitation;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisteredEventTest extends TestCase
{
    public function testWelcomeNotificationSend()
    {
        $user = factory(User::class)
            ->create();

        Notification::fake();

        event(new Registered($user));

        Notification::assertSentTo(
            $user,
            function (WelcomeNotification $notification, $channels) use ($user) {
                return $notification->user->id === $user->id;
            }
        );
    }
}
