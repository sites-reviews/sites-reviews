<?php

namespace Tests\Feature\Site\Review;

use App\Notifications\ConfirmationOfCreatingReviewNotification;
use App\Notifications\InvitationNotification;
use App\Review;
use App\Site;
use App\TempReview;
use App\User;
use Carbon\Carbon;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SiteReviewStoreTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
/*
        // prevent validation error on captcha
        NoCaptcha::shouldReceive('verifyResponse')
            ->once()
            ->andReturn(true);
        */
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIfAuth()
    {
        $user = factory(User::class)
            ->create();

        $site = factory(Site::class)
            ->create();

        $review = factory(Review::class)
            ->make();

        Carbon::setTestNow(now()->addMinute());

        $response = $this->actingAs($user)
            ->post(route('reviews.store', ['site' => $site]), [
                'advantages' => $review->advantages,
                'disadvantages' => $review->disadvantages,
                'comment' => $review->comment,
                'rate' => $review->rate
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas(['success' => __('review.successfully_published')]);

        $createdReview = $user->reviews()->first();

        $user->refresh();
        $site->refresh();

        $response->assertRedirect(route('sites.show', ['site' => $site]).'#'.$createdReview->getAnchorName());

        $this->assertNotNull($createdReview);
        $this->assertEquals($review->advantages, $createdReview->advantages);
        $this->assertEquals($review->disadvantages, $createdReview->disadvantages);
        $this->assertEquals($review->comment, $createdReview->comment);
        $this->assertEquals($review->rate, $createdReview->rate);
        $this->assertEquals($site->updated_at, $site->latest_rating_changes_at);

        $this->assertEquals(1, $user->number_of_reviews);
        $this->assertEquals(1, $site->number_of_reviews);

        $this->assertTrue($createdReview->is($site->reviews()->first()));

        $this->assertEquals($review->rate, $site->rating);
    }

    public function testIfGuest()
    {
        Notification::fake();

        $site = factory(Site::class)
            ->create();

        $reviewNew = TempReview::factory()
            ->make();

        $userNew = factory(User::class)
            ->make();

        $array = $reviewNew->toArray();

        $array['name'] = $userNew->name;
        $array['email'] = $userNew->email;

        $response = $this
            ->post(route('reviews.store', ['site' => $site]), $array)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $review = $site->tempReviews()->first();

        $this->assertNotNull($review);
        $this->assertEquals($review->email, $userNew->email);

        $response->assertRedirect(route('reviews.show.temp', ['uuid' => $review->uuid]));

        Notification::assertSentTo(
            (new AnonymousNotifiable())->route('email', $review->email),
            ConfirmationOfCreatingReviewNotification::class);

        Notification::assertNotSentTo(
            new AnonymousNotifiable(),
            InvitationNotification::class
        );
    }
}
