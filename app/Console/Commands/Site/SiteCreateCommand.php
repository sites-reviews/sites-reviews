<?php

namespace App\Console\Commands\Site;

use App\Image;
use App\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Litlife\Url\Url;
use Spatie\Browsershot\Browsershot;

class SiteCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:create {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Комманда добавляет новый сайт, если его еще нет в базе данных';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $url;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->url = Url::fromString($this->argument('url'));

        if (empty($this->url->getHost()))
        {
            $this->error(__('Wrong URL'));
            return false;
        }

        $site = Site::url($this->url->getHost())
            ->first();

        if (!empty($site)) {
            $this->error(__('The site is already in the database'));
        } else {

            $site = new Site();
            $site->domain = $this->url->getHost();
            $site->title = $this->url->getHost();
            $site->autoAssociateAuthUser();
            $site->update_the_preview = true;
            $site->update_the_page = true;
            $site->save();

            $this->info(__('The site was added successfully'));
        }
    }

    public function getMetaData()
    {
        return \OpenGraph::fetch((string)$this->url, true);
    }
}
