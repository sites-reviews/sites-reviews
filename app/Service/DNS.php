<?php

namespace App\Service;

use Illuminate\Support\Collection;

class DNS
{
    public function getRecord($host, $type) : Collection
    {
        return collect(dns_get_record($host, $type));
    }
}
