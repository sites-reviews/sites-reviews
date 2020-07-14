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
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LoginEventTest extends TestCase
{
    public function testTryRestoreLocaleIfLocaleIsSaved()
    {
        $user = factory(User::class)
            ->create(['selected_locale' => 'en']);

        Notification::fake();

        $this->assertNull(session('locale'));

        event(new Login('guard', $user, true));

        $this->assertEquals('en', session('locale'));
    }

    public function testNotRestoreLocaleIfLocaleIsNotSaved()
    {
        $user = factory(User::class)
            ->create(['selected_locale' => null]);

        Notification::fake();

        $this->assertNull(session('locale'));

        event(new Login('guard', $user, true));

        $this->assertNull(session('locale'));
    }
}
