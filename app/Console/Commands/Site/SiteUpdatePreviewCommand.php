<?php

namespace App\Console\Commands\Site;

use App\Image;
use App\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Litlife\Url\Url;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class SiteUpdatePreviewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:screenshot_update {site}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Комманда получает сайт и создает скриншот изображения сайта';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $site;

    /**
     * Execute the console command.
     *
     * @param Browsershot $browsershot
     * @return mixed
     * @throws
     */
    public function handle(Browsershot $browsershot)
    {
        $this->site = $this->getSite($this->argument('site'));

        try {
            DB::transaction(function () use ($browsershot) {

                $content = $browsershot
                    ->url((string)$this->site->getUrl())
                    ->timeout(60)
                    ->windowSize(1000, 1000)
                    ->setScreenshotType('jpeg', 100)
                    ->setDelay(5000)
                    ->dismissDialogs()
                    ->ignoreHttpsErrors()
                    ->screenshot();

                $image = new Image;
                $image->open($content, 'blob');
                $image->save();

                $this->site->preview()->associate($image);
                $this->site->number_of_attempts_update_the_preview = 0;
                $this->site->save();

                $this->info(__('The site preview was updated successfully'));
            });

            return true;

        } catch (ProcessTimedOutException $exception) {

            return $this->failedAttempt();

        } catch (ProcessFailedException $exception) {

            if (!$this->isPuppeterNetworkError($exception)) {
                report($exception);
            }

            return $this->failedAttempt();
        }
    }

    public function getSite($site)
    {
        if (intval($site))
            return Site::findOrFail($site);
        else {
            $url = Url::fromString($site);

            return Site::whereDomain($url->getHost())->firstOrFail();
        }
    }

    public function failedAttempt()
    {
        $this->site->number_of_attempts_update_the_preview++;

        if ($this->site->number_of_attempts_update_the_preview >= 3)
            $this->site->update_the_preview = false;

        $this->site->save();

        $this->error(__('Error updating the site preview'));

        return false;
    }

    public function isPuppeterNetworkError(ProcessFailedException $exception)
    {
        $proccess = $exception->getProcess();

        if ($proccess->getExitCode() == 1 and $proccess->getExitCodeText() == 'General error') {

            if (preg_match('/Error\:\ net\:\:([A-Z\_]+)\ at/iu', $proccess->getErrorOutput(), $matches)) {

                if (in_array($matches[1], [
                    'ERR_CONNECTION_RESET',
                    'ERR_INVALID_RESPONSE',
                    'ERR_CONNECTION_REFUSED',
                    'ERR_NAME_NOT_RESOLVED',
                    'ERR_EMPTY_RESPONSE',
                    'ERR_HTTP2_PROTOCOL_ERROR',
                    'ERR_CONNECTION_CLOSED',
                    'ERR_TIMED_OUT'
                ],
                )) {
                    return true;
                }
            }
        }

        return false;
    }
}
