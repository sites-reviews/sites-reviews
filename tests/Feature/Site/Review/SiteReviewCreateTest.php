<?php

namespace Tests\Feature\Site\Review;

use App\Notifications\ConfirmationOfCreatingReviewNotification;
use App\Review;
use App\Site;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SiteReviewCreateTest extends TestCase
{
    public function testIsOk()
    {
        $site = factory(Site::class)
            ->create();

        $this->get(route('reviews.create', ['site' => $site]))
            ->assertOk();
    }

    public function testRedirectIfAlreadyExists()
    {
        $review = factory(Review::class)
            ->create();

        $site = $review->site;
        $user = $review->create_user;

        $this->actingAs($user)
            ->get(route('reviews.create', ['site' => $site]))
            ->assertRedirect(route('reviews.edit', ['review' => $review]));
    }
}
