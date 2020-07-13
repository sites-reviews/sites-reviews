<?php

namespace App\Jobs;

use App\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Litlife\Url\Url;

class SiteCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $url)
    {
        $this->url = Url::fromString($url);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $site = Site::url($this->url->getHost())
            ->first();

        if (empty($site))
        {
            $site = new Site();
            $site->domain = $this->url->getHost();
            $site->title = $this->url->getHost();
            $site->autoAssociateAuthUser();
            $site->save();
        }
    }
}
