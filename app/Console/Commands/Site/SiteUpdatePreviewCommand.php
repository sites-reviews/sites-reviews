<?php

namespace App\Console\Commands\Site;

use App\Image;
use App\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;

class SiteUpdatePreviewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:screenshot_update {site_id}';

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
        $this->site = Site::findOrFail($this->argument('site_id'));

        try {
            DB::transaction(function () use ($browsershot) {

                $content = $browsershot
                    ->url((string)$this->site->getUrl())
                    ->windowSize(1000, 1000)
                    ->setScreenshotType('jpeg', 100)
                    ->setDelay(5000)
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

        } catch (\Exception $exception) {
            report($exception);

            $this->site->number_of_attempts_update_the_preview++;

            if ($this->site->number_of_attempts_update_the_preview >= 3)
                $this->site->update_the_preview = false;

            $this->site->save();

            $this->error(__('Error updating the site preview'));

            return false;
        }
    }
}
