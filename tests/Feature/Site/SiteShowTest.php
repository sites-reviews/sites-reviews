<?php

namespace Tests\Feature\Site;

use App\Review;
use App\Site;
use App\TempReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class SiteShowTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShow()
    {
        $site = factory(Site::class)
            ->create();

        $this->get(route('sites.show', $site->domain))
            ->assertOk()
            ->assertViewHas('site', $site)
            ->assertViewHas('authReview', null)
            ->assertViewHas('reviews_order_by', 'latest');
    }

    public function testShowNotFound()
    {
        $site = factory(Site::class)
            ->create();

        $this->get(route('sites.show', ['site' => Str::random(8).'.com']))
            ->assertNotFound();
    }

    public function testRoute()
    {
        $site = factory(Site::class)
            ->create();

        $this->assertEquals(config('app.url').'/'.config('app.locale').'/'.$site->domain,
            route('sites.show', $site));
    }

    public function testShowLatestReviewsOrder()
    {
        $review = factory(Review::class)
            ->create();

        $site = $review->site;

        $response = $this->get(route('sites.show', ['site' => $site, 'reviews_order_by' => 'latest']))
            ->assertOk()
            ->assertViewHas('reviews_order_by', 'latest');

        $reviews = $response->original->gatherData()['reviews'];

        $this->assertEquals(1, $reviews->count());
    }

    public function testShowReviewsOrderRatingDesc()
    {
        $review = factory(Review::class)
            ->create();

        $site = $review->site;

        $response = $this->get(route('sites.show', ['site' => $site, 'reviews_order_by' => 'rating_desc']))
            ->assertOk()
            ->assertViewHas('reviews_order_by', 'rating_desc');

        $reviews = $response->original->gatherData()['reviews'];

        $this->assertEquals(1, $reviews->count());
    }

    public function testAuthUserReviewViewParameter()
    {
        $review = factory(Review::class)
            ->create();

        $site = $review->site;
        $create_user = $review->create_user;

        $response = $this->actingAs($create_user)
            ->get(route('sites.show', ['site' => $site]))
            ->assertOk()
            ->assertViewHas('authReview', $review);

        $reviews = $response->original->gatherData()['reviews'];

        $this->assertEquals(0, $reviews->count());
    }

    public function testShowSubDomain()
    {
        $site = factory(Site::class)
            ->create(['domain' => 'www.test.com']);

        $this->get(route('sites.show', $site->domain))
            ->assertOk()
            ->assertViewHas('site', $site);
    }

    public function testShowRuDomain()
    {
        $site = factory(Site::class)
            ->states('with_cyrillic_domain')
            ->create();

        $this->get(route('sites.show', $site->domain))
            ->assertOk()
            ->assertViewHas('site', $site);
    }

    public function testOwnerVar()
    {
        $site = factory(Site::class)
            ->states('with_owner')
            ->create();

        $owner = $site->userOwner;

        $this->assertNotNull($owner);

        $this->get(route('sites.show', $site->domain))
            ->assertOk()
            ->assertViewHas('owner', $owner);
    }

    public function testShowTemp()
    {
        $review = TempReview::factory()
            ->create();

        $this->get(route('reviews.show.temp', ['uuid' => $review->uuid]))
            ->assertOk();
    }
}
