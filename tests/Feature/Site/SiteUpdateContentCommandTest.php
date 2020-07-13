<?php

namespace Tests\Feature\Site;

use App\Review;
use App\Service\DNS;
use App\Service\UrlContent;
use App\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use shweshi\OpenGraph\Facades\OpenGraphFacade;
use Tests\TestCase;

class SiteUpdateContentCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUpdateSuccessfully()
    {
        $site = factory(Site::class)
            ->create();

        $title = $this->faker->realText(100);
        $description = $this->faker->realText(300);

        $html = $this->faker->realText(300);

        $this->mock(UrlContent::class, function ($mock) use ($html) {
            $mock->shouldReceive('getContent')
                ->once()
                ->andReturn($html);
        });

        $this->artisan('site:update_content', ['site_id' => $site->id])
            ->expectsOutput(__('Site content was updated successfully'))
            ->assertExitCode(1);

        $site->refresh();

        $this->assertEquals($html, $site->page->content);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFail()
    {
        $site = factory(Site::class)
            ->create([
                'update_the_page' => true,
                'number_of_attempts_update_the_page' => 0
            ]);

        $this->mock(UrlContent::class, function ($mock) {
            $mock->shouldReceive('getContent')
                ->once()
                ->andThrow(\Exception::class);
        });

        $this->artisan('site:update_content', ['site_id' => $site->id])
            ->expectsOutput(__('Error updating site content'))
            ->assertExitCode(0);

        $site->refresh();

        $this->assertEquals('', $site->page->content);
        $this->assertTrue($site->update_the_page);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testTheNumberOfAttemptsIsExceeded()
    {
        $site = factory(Site::class)
            ->create([
                'update_the_page' => true,
                'number_of_attempts_update_the_page' => 2
            ]);

        $this->assertEquals(2, $site->number_of_attempts_update_the_page);

        $this->mock(UrlContent::class, function ($mock) {
            $mock->shouldReceive('getContent')
                ->once()
                ->andThrow(\Exception::class);
        });

        $this->artisan('site:update_content', ['site_id' => $site->id])
            ->assertExitCode(0);

        $site->refresh();

        $this->assertEquals('', $site->page->content);
        $this->assertEquals(3, $site->number_of_attempts_update_the_page);
        $this->assertFalse($site->update_the_page);
    }
}
