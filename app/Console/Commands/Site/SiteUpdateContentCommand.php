<?php

namespace App\Console\Commands\Site;

use App\Image;
use App\Service\UrlContent;
use App\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Litlife\Url\Url;
use Spatie\Browsershot\Browsershot;

class SiteUpdateContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:update_content {site_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Комманда обновляет содержимое сайта';

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
     * @param UrlContent $urlContent
     * @return mixed
     */
    public function handle(UrlContent $urlContent)
    {
        $this->site = Site::findOrFail($this->argument('site_id'));

        try {
            $content = $urlContent->getContent($this->site->getUrl());

            $this->site->page->content = $content;
            $this->site->updateDescriptionFromPage();
            $this->site->number_of_attempts_update_the_page = 0;
            $this->site->push();

            $this->info(__('Site content was updated successfully'));

            return true;

        } catch (\Exception $exception) {

            $this->site->number_of_attempts_update_the_page++;

            if ($this->site->number_of_attempts_update_the_page >= 3)
                $this->site->update_the_page = false;

            $this->site->save();

            $this->info(__('Error updating site content'));

            return false;
        }
    }
}
