<?php

namespace App\Observers;

use App\Site;

class SiteObserver
{
    public function creating(Site $site)
    {
        $site->latest_rating_changes_at = now();
    }
}
