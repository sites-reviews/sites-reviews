<?php

namespace App;

use App\Enums\Gender;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * App\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property int $number_of_reviews
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereNumberOfReviews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\User withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Review[] $reviews
 * @property-read int|null $reviews_count
 * @property int $rating
 * @property int|null $avatar_image_id
 * @property int $gender
 * @property-read \App\Image|null $avatar
 * @property-read \App\UserNotificationSetting $notificationSetting
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatarImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRating($value)
 * @property int|null $avatar_preview_image_id
 * @property-read \App\Image|null $avatarPreview
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PasswordReset[] $passwordResets
 * @property-read int|null $password_resets_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatarPreviewImageId($value)
 */
class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public function reviews()
    {
        return $this->hasMany('App\Review', 'create_user_id', 'id');
    }

    public function updateNumberOfReviews()
    {
        $this->number_of_reviews = $this->reviews()->count();
    }

    public function updateRating()
    {
        $this->rating = $this->reviews()->sum('rating');
    }

    public function avatar()
    {
        return $this->belongsTo('App\Image', 'avatar_image_id', 'id');
    }

    public function avatarPreview()
    {
        return $this->belongsTo('App\Image', 'avatar_preview_image_id', 'id');
    }

    public function setGenderAttribute($value)
    {
        $this->attributes['gender'] = Gender::getValue($value);
    }

    public function getGenderAttribute($value)
    {
        if (empty($value))
            $value = Gender::unknown;

        return Gender::getKey($value);
    }

    public function notificationSetting()
    {
        return $this->hasOne('App\UserNotificationSetting', 'user_id', 'id')
            ->withDefault();
    }

    public function dropdownNotifications()
    {
        return $this->notifications()
            ->orderBy('read_at', 'asc')
            ->limit(10)
            ->get();
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function passwordResets()
    {
        return $this->hasMany('App\PasswordReset', 'user_id', 'id');
    }

    public function setSelectedLocaleAttribute($value)
    {
        $value = trim($value);
        $value = mb_strtolower($value);

        if (!in_array($value, config('app.locales')))
            $value = null;

        $this->attributes['selected_locale'] = $value;
    }
}
