<?php

namespace App\Observers;

use App\Image;
use App\Notifications\InvitationNotification;
use App\Notifications\ReviewWasLikedNotification;
use App\Review;
use App\ReviewRating;
use App\Site;
use App\User;
use App\UserInvitation;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use Litlife\Url\Url;

class UserInvitationObserver
{
    public function creating(UserInvitation $invitation)
    {
        $invitation->token = mb_strtolower(Str::random(64));
    }

    public function created(UserInvitation $invitation)
    {

    }
}
