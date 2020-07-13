<?php

namespace App\Observers;

use App\Image;
use App\Notifications\InvitationNotification;
use App\Notifications\PasswordResetNotification;
use App\Notifications\ReviewWasLikedNotification;
use App\PasswordReset;
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

class PasswordResetObserver
{
    public function creating(PasswordReset $passwordReset)
    {
        $passwordReset->token = mb_strtolower(Str::random(64));
    }

    public function created(PasswordReset $passwordReset)
    {
        $passwordReset->user->notify(new PasswordResetNotification($passwordReset));
    }
}
