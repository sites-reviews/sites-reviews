<?php

namespace App\Observers;

use App\Review;
use App\Site;
use App\TempReview;
use App\User;
use Illuminate\Support\Str;

class TempReviewObserver
{
    public function creating(TempReview $review)
    {
        $review->uuid = Str::uuid();
    }
}
