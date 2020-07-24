<?php

namespace App\Console\Commands\Site;

use App\Image;
use App\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;

class SitePreviewsUpdateWaitingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:update_preview_waiting {count=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Комманда обновляет превью у ожидающих сайтов';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $count;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->count = $this->argument('count');

        Site::where('update_the_preview', true)
            ->orderManuallyAddedFirst()
            ->chunkById($this->count, function ($sites) {
                foreach ($sites as $site) {
                    $this->site($site);
                }
            });
    }

    public function site(Site $site)
    {
        if ($this->call('site:screenshot_update', ['site_id' => $site->id]))
        {
            $site->update_the_preview = false;
            $site->save();
        }
    }
}
