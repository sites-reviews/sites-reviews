<?php

namespace Tests\Feature\Site;

use App\Console\Commands\Site\SiteUpdateContentCommand;
use App\Review;
use App\Service\DNS;
use App\Service\UrlContent;
use App\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
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
    public function testUpdateSuccessfullyGetEncodingFromHeader()
    {
        $site = factory(Site::class)
            ->create();

        $title = $this->faker->realText(100);
        $description = $this->faker->realText(300);

        $input = <<<EOF
<html>
<head>
</head>
<body>
привет
</body>
</html>
EOF;

        $response = new Response(200, [
            'Content-Type' => 'text/html; charset=windows-1251'
        ], iconv('utf-8', 'windows-1251', $input));

        $this->mock(Client::class, function ($mock) use ($response) {
            $mock->shouldReceive('request')
                ->once()
                ->andReturn($response);
        });

        $this->artisan('site:update_content', ['site' => $site->id])
            ->expectsOutput(__('Site content was updated successfully'))
            ->assertExitCode(1);

        $site->refresh();

        $output = <<<EOF
<html>
<head>
</head>
<body>
привет
</body>
</html>
EOF;

        $this->assertEquals($output, $site->page->content);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUpdateSuccessfullyGetEncodingFromHtml()
    {
        $site = factory(Site::class)
            ->create();

        $title = $this->faker->realText(100);
        $description = $this->faker->realText(300);

        $html = <<<EOF
<html>
<head>
<meta charset="windows-1251" />
</head>
<body>
привет
</body>
</html>
EOF;

        $response = new Response(200, [], iconv('utf-8', 'windows-1251', $html));

        $this->mock(Client::class, function ($mock) use ($response) {
            $mock->shouldReceive('request')
                ->once()
                ->andReturn($response);
        });

        $this->artisan('site:update_content', ['site' => $site->id])
            ->expectsOutput(__('Site content was updated successfully'))
            ->assertExitCode(1);

        $site->refresh();

        $output = <<<EOF
<html>
<head>

</head>
<body>
привет
</body>
</html>
EOF;

        $this->assertEquals($output, $site->page->content);
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

        $this->mock(Client::class, function ($mock) {
            $mock->shouldReceive('request')
                ->once()
                ->andThrow(new ConnectException('123', new Request('get', '')));
        });

        $this->artisan('site:update_content', ['site' => $site->id])
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

        $this->mock(Client::class, function ($mock) {
            $mock->shouldReceive('request')
                ->once()
                ->andThrow(new ConnectException('123', new Request('get', '')));
        });

        $this->artisan('site:update_content', ['site' => $site->id])
            ->assertExitCode(0);

        $site->refresh();

        $this->assertEquals('', $site->page->content);
        $this->assertEquals(3, $site->number_of_attempts_update_the_page);
        $this->assertFalse($site->update_the_page);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHtmlHeaderIsPriorityInsteadEncoding()
    {
        $site = factory(Site::class)
            ->create();

        $title = $this->faker->realText(100);
        $description = $this->faker->realText(300);

        $input = <<<EOF
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
</head>
<body>
&#160;&#160;&#160;
</body>
</html>
EOF;

        $response = new Response(200, [
            'Content-Type' => 'text/html; charset=UTF-8'
        ], iconv('utf-8','iso-8859-1', $input));

        $this->mock(Client::class, function ($mock) use ($response) {
            $mock->shouldReceive('request')
                ->once()
                ->andReturn($response);
        });

        $this->artisan('site:update_content', ['site' => $site->id])
            ->expectsOutput(__('Site content was updated successfully'))
            ->assertExitCode(1);

        $site->refresh();

        $output = <<<EOF
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
</head>
<body>
&#160;&#160;&#160;
</body>
</html>
EOF;

        $this->assertEquals($output, $site->page->content);
    }
/*
    public function test()
    {
        $site = factory(Site::class)
            ->create(['domain' => 'japan-guide.com']);

        $this->artisan('site:update_content', ['site_id' => $site->id])
            ->expectsOutput(__('Site content was updated successfully'))
            ->assertExitCode(1);
    }
    */

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetSiteIfArgumentUrl()
    {
        $site = factory(Site::class)
            ->create();

        $command = new SiteUpdateContentCommand();

        $this->assertTrue($site->is($command->getSite((string)$site->getUrl())));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetSiteIfArgumentNumber()
    {
        $site = factory(Site::class)
            ->create();

        $command = new SiteUpdateContentCommand();

        $this->assertTrue($site->is($command->getSite($site->id)));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCp1251encoding()
    {
        $site = factory(Site::class)
            ->create();

        $title = $this->faker->realText(100);
        $description = $this->faker->realText(300);

        $html = <<<EOF
<html>
<head>
<meta charset="utf-8" />
</head>
<body>
привет
</body>
</html>
EOF;

        $response = new Response(200, [
            'Content-Type' => 'text/html; charset=cp1251'
        ], iconv('utf-8', 'cp1251', $html));

        $this->mock(Client::class, function ($mock) use ($response) {
            $mock->shouldReceive('request')
                ->once()
                ->andReturn($response);
        });

        $this->artisan('site:update_content', ['site' => $site->id])
            ->expectsOutput(__('Site content was updated successfully'))
            ->assertExitCode(1);

        $site->refresh();

        $output = <<<EOF
<html>
<head>

</head>
<body>
привет
</body>
</html>
EOF;

        $this->assertEquals($output, $site->page->content);
    }

}
