<?php

namespace App;

use App\Traits\NestedItems;
use App\Traits\UpAndDownRateableTrait;
use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Comment
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $review_id
 * @property int $create_user_id
 * @property string $text
 * @property int $rating
 * @property int $rate_up
 * @property int $rate_down
 * @property int $children_count
 * @property int $level
 * @property string|null $tree
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CommentRating[] $authUserRatings
 * @property-read int|null $auth_user_votes_count
 * @property-read \App\User $create_user
 * @property-read mixed $level_with_limit
 * @property mixed $parent
 * @property-read mixed $root
 * @property-read mixed $tree_array
 * @property-read \App\Review $review
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CommentRating[] $ratings
 * @property-read int|null $ratings_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment childs($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment descendants($ids)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment orDescendants($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment roots()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereReviewId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereTree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereVoteDown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereVoteUp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Comment withoutTrashed()
 * @property-read int|null $auth_user_ratings_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereRateDown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereRateUp($value)
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment any()
 */
class Comment extends Model
{
    use UserCreate;
    use SoftDeletes;
    use NestedItems;
    use UpAndDownRateableTrait;

    public const RATEABLE_MODEL = 'App\CommentRating';

    public $fillable = [
        'text'
    ];

    public $visible = [
        'text',
        'created_at',
        'deleted_at'
    ];

    public function scopeAny($query)
    {
        return $query->withTrashed();
    }

    public function review()
    {
        return $this->belongsTo('App\Review')
            ->any();
    }

    public function getRedirectToUrl()
    {
        return route('comments.go_to', $this);
    }

    public function isCreatorIsSiteOwner() :bool
    {
        return $this->create_user->is($this->review->site->userOwner);
    }
}
