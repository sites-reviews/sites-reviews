<?php

namespace Tests\Feature\Site\Page;

use App\Review;
use App\Site;
use App\SitePage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class CharacterNotInRepertoireErrorTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test()
    {
        $bytes = pack("H*", "E32E2E");

        $page = factory(SitePage::class)
            ->create(['content' => '']);

        $page->content = $bytes;
        $page->save();

        $this->assertEquals('?..', $page->content);
    }
}
