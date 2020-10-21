<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use App\Site;
use App\TempReview;
use App\User;
use App\UserInvitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class SiteReviewConfirmTest extends TestCase {

    public function testOk()
    {
        Event::fake(Registered::class);

        $tempReview = TempReview::factory()
            ->create();

        $this->get(route('reviews.confirm', ['review' => $tempReview, 'token' => $tempReview->token]))
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas('success', __('A review is successfully published'));

        $tempReview->refresh();

        $user = User::whereEmail($tempReview->email)->first();

        $this->assertNotNull($user);

        $review = $user->reviews()->first();

        $this->assertNotNull($review);

        $this->assertAuthenticatedAs($user);
        $this->assertTrue($review->isAccepted());
        $this->assertNotNull($user->email_verified_at);

        $this->assertEquals($tempReview->advantages, $review->advantages);
        $this->assertEquals($tempReview->disadvantages, $review->disadvantages);
        $this->assertEquals($tempReview->comment, $review->comment);
        $this->assertEquals($tempReview->rate, $review->rate);

        $this->assertTrue($tempReview->trashed());

        Event::assertDispatched(Registered::class);
    }

    public function testIfOtherPublishedReviewExists()
    {
        Event::fake(Registered::class);

        $user = factory(User::class)
            ->create();

        $site = factory(Site::class)
            ->create();

        $review = factory(Review::class)
            ->states('accepted')
            ->create(['create_user_id' => $user->id, 'site_id' => $site->id]);

        $tempReview = TempReview::factory()
            ->create(['email' => $user->email, 'site_id' => $site->id]);

        $response = $this->get(route('reviews.confirm', ['review' => $tempReview, 'token' => $tempReview->token]))
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas('success', __('You already have a review for this site. Your new review is saved as a draft'));

        $this->assertEquals(2, $site->reviews()->count());
        $this->assertEquals(2, $user->reviews()->count());

        $user->refresh();
        $review->refresh();
        $tempReview->refresh();

        $review2 = $user->reviews()
            ->where('id', '!=', $review->id)
            ->first();

        $response->assertRedirect(route('sites.show', ['site' => $site]).'#'.$review2->getAnchorName());

        $this->assertAuthenticatedAs($user);
        $this->assertTrue($review->isAccepted());
        $this->assertTrue($review2->isPrivate());

        $this->assertTrue($tempReview->trashed());

        Event::assertNotDispatched(Registered::class);
    }

    public function testInvalidToken()
    {
        $tempReview = TempReview::factory()
            ->create();

        $response = $this->get(route('reviews.confirm', ['review' => $tempReview, 'token' => Str::random()]))
            ->assertNotFound()
            ->assertSeeText(__('The link is incorrect or outdated'));
    }
}
