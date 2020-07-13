<?php

namespace Tests\Feature\Event;

use App\Notifications\InvitationNotification;
use App\Notifications\NewResponseToReviewNotification;
use App\Notifications\PasswordChangedSuccessfullyNotification;
use App\Notifications\ReviewWasLikedNotification;
use App\Comment;
use App\ReviewRating;
use App\User;
use App\UserInvitation;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetEventTest extends TestCase
{
    public function testNotificationSend()
    {
        $user = factory(User::class)
            ->create();

        Notification::fake();

        event(new PasswordReset($user));

        Notification::assertSentTo(
            $user,
            function (PasswordChangedSuccessfullyNotification $notification, $channels) use ($user) {
                return $notification->user->id === $user->id;
            }
        );
    }
}
