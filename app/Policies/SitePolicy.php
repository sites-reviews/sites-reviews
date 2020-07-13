<?php

namespace App\Policies;

use App\Site;
use App\User;
use Illuminate\Auth\Access\Response;

class SitePolicy extends Policy
{
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function show(?User $authUser, Site $site)
    {
        return $this->allow('you can get access to view this site');
    }
}
