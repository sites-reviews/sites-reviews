<?php

namespace App\Observers;

use App\Image;
use App\ProofOwnership;
use App\Review;
use App\Site;
use App\User;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use Litlife\Url\Url;

class ProofOwnershipObserver
{
    public function creating(ProofOwnership $proof)
    {
        $proof->dns_code = mb_strtolower(Str::random(32));
        $proof->file_path = mb_strtolower(config('verification.dns_key_name').'-'.Str::random(32).'.txt');
        $proof->file_code = mb_strtolower(Str::random(32));
    }
}
