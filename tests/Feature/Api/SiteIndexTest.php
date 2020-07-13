<?php

namespace Tests\Feature\Api;

use App\Http\Resources\SiteResource;
use App\Notifications\NewResponseToReviewNotification;
use App\Review;
use App\Comment;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SiteIndexTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRouteIsOk()
    {
        $site = factory(Site::class)
            ->create()
            ->fresh();

        $site2 = factory(Site::class)
            ->create()
            ->fresh();

        $sites = Site::whereIn('domain', [$site->domain, $site2->domain])->get();

        $json = SiteResource::collection($sites)->toArray(request());

        $this->get(route('api.sites.index', ['sites' => [$site->domain, $site2->domain]]))
            ->assertOk()
            ->assertJson(['data' => $json]);
    }

    public function testEmpty()
    {
        $this->get(route('api.sites.index'))
            ->assertOk();
    }
}
