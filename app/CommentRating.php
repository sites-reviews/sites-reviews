<?php

namespace App;

use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\CommentRating
 *
 * @property int $id
 * @property int $rateable_id
 * @property int $rating
 * @property int $create_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User $create_user
 * @property-read \App\Comment $rateable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\CommentRating onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating whereRateableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating whereVote($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CommentRating withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\CommentRating withoutTrashed()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CommentRating whereRating($value)
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Model void()
 */
class CommentRating extends Model
{
    use SoftDeletes;
    use UserCreate;

    public function rateable()
    {
        return $this->belongsTo('App\Comment', 'rateable_id', 'id');
    }
}
