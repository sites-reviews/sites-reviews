<?php

namespace Tests\Feature\Api;

use App\Notifications\NewResponseToReviewNotification;
use App\Review;
use App\Comment;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Http\Resources\SiteResource;

class SiteShowTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRouteIsOk()
    {
        $site = factory(Site::class)
            ->create();

        $sites = Site::whereIn('domain', [$site->domain])->get();

        $json = SiteResource::collection($sites)->toArray(request());

        $this->get(route('api.sites.show', ['site' => $site->domain]))
            ->assertOk()
            ->assertJson(['data' => $json]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNotFound()
    {
        $this->get(route('api.sites.show', ['site' => Str::random(8).'.'.Str::random(2)]))
            ->assertOk();
    }
}
