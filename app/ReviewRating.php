<?php

namespace App;

use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\ReviewRating
 *
 * @property int $id
 * @property int $review_id
 * @property int $rating
 * @property int $create_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating whereReviewId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating whereVote($value)
 * @mixin \Eloquent
 * @property int $rateable_id
 * @property-read \App\User $create_user
 * @property-read \App\Review $rateable
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewRating onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating whereRateableId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewRating withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewRating withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ReviewRating whereRating($value)
 */
class ReviewRating extends Model
{
    use SoftDeletes;
    use UserCreate;

    public function rateable()
    {
        return $this->belongsTo('App\Review', 'rateable_id', 'id');
    }
}
