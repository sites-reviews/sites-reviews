<?php

namespace Tests\Feature\Site;

use App\Console\Commands\Site\SiteUpdateContentCommand;
use App\Console\Commands\Site\SiteUpdatePreviewCommand;
use App\Review;
use App\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class SiteUpdatePreviewCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUpdateSuccessfully()
    {
        $site = factory(Site::class)
            ->create([
                'update_the_preview' => true
            ]);

        $this->assertNull($site->preview);
        $this->assertTrue($site->update_the_preview);
        $this->assertEquals(0, $site->number_of_attempts_update_the_preview);

        $fakeImage = $this->fakeImageStream();

        $this->mock(Browsershot::class, function ($mock) use ($site, $fakeImage) {
            $mock->shouldReceive('url')
                ->with((string)$site->getUrl())
                ->andReturn($mock)
                ->shouldReceive('timeout')
                ->with(60)
                ->andReturn($mock)
                ->shouldReceive('windowSize')
                ->with(1000, 1000)
                ->andReturn($mock)
                ->shouldReceive('setScreenshotType')
                ->with('jpeg', 100)
                ->andReturn($mock)
                ->shouldReceive('setDelay')
                ->with(5000)
                ->andReturn($mock)
                ->shouldReceive('dismissDialogs')
                ->andReturn($mock)
                ->shouldReceive('ignoreHttpsErrors')
                ->andReturn($mock)
                ->shouldReceive('screenshot')
                ->andReturn(file_get_contents($fakeImage));
        });

        $this->artisan('site:screenshot_update', ['site' => $site->id])
            ->expectsOutput(__('The site preview was updated successfully'))
            ->assertExitCode(1);

        $site->refresh();

        $this->assertNotNull($site->preview);
        $this->assertTrue($site->update_the_preview);
        $this->assertEquals(0, $site->number_of_attempts_update_the_preview);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetSiteIfArgumentUrl()
    {
        $site = factory(Site::class)
            ->create();

        $command = new SiteUpdatePreviewCommand();

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

        $command = new SiteUpdatePreviewCommand();

        $this->assertTrue($site->is($command->getSite($site->id)));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFailed()
    {
        $site = factory(Site::class)
            ->create([
                'update_the_preview' => true
            ]);

        $this->assertNull($site->preview);
        $this->assertEquals(0, $site->number_of_attempts_update_the_preview);

        $this->mock(Browsershot::class, function ($mock) use ($site) {
            $mock->shouldReceive('url')
                ->with((string)$site->getUrl())
                ->andThrow(new ProcessTimedOutException(new Process(['test']), ProcessTimedOutException::TYPE_GENERAL));
        });

        $this->artisan('site:screenshot_update', ['site' => $site->id])
            ->assertExitCode(0)
            ->expectsOutput(__('Error updating the site preview'));

        $site->refresh();

        $this->assertNull($site->preview);
        $this->assertEquals(1, $site->number_of_attempts_update_the_preview);
        $this->assertTrue($site->update_the_preview);
    }

    public function testTheNumberOfAttemptsIsExceeded()
    {
        $site = factory(Site::class)
            ->create([
                'update_the_preview' => true,
                'number_of_attempts_update_the_preview' => 2
            ]);

        $this->assertNull($site->preview);
        $this->assertEquals(2, $site->number_of_attempts_update_the_preview);

        $this->mock(Browsershot::class, function ($mock) use ($site) {
            $mock->shouldReceive('url')
                ->with((string)$site->getUrl())
                ->andThrow(new ProcessTimedOutException(new Process(['test']), ProcessTimedOutException::TYPE_GENERAL));
        });

        $this->artisan('site:screenshot_update', ['site' => $site->id])
            ->assertExitCode(0)
            ->expectsOutput(__('Error updating the site preview'));

        $site->refresh();

        $this->assertNull($site->preview);
        $this->assertEquals(3, $site->number_of_attempts_update_the_preview);
        $this->assertFalse($site->update_the_preview);
    }

    public function testIsPuppeterNetworkErrorIsTrue()
    {
        $process = \Mockery::mock(Process::class);

        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getExitCode')->twice()->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('General error');
        $process->shouldReceive('getCommandLine')->andReturn('test');
        $process->shouldReceive('getWorkingDirectory')->andReturn('');
        $process->shouldReceive('isOutputDisabled')->andReturn(false);
        $process->shouldReceive('getOutput')->andReturn('');
        $process->shouldReceive('getErrorOutput')->andReturn('Error: net::ERR_NAME_NOT_RESOLVED at http://www.example.com at navigate');

        $exception = new ProcessFailedException($process);

        $command = new SiteUpdatePreviewCommand();

        $this->assertTrue($command->isPuppeterNetworkError($exception));
    }

    public function testIsPuppeterNetworkErrorIsFalse()
    {
        $process = \Mockery::mock(Process::class);

        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getExitCode')->twice()->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('General error');
        $process->shouldReceive('getCommandLine')->andReturn('test');
        $process->shouldReceive('getWorkingDirectory')->andReturn('');
        $process->shouldReceive('isOutputDisabled')->andReturn(false);
        $process->shouldReceive('getOutput')->andReturn('');
        $process->shouldReceive('getErrorOutput')->andReturn('Error: net::SOME_ERROR at http://www.example.com at navigate');

        $exception = new ProcessFailedException($process);

        $command = new SiteUpdatePreviewCommand();

        $this->assertFalse($command->isPuppeterNetworkError($exception));
    }
}
