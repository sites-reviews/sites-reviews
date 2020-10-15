<?php

namespace App;

use App\Traits\NestedItems;
use App\Traits\UpAndDownRateableTrait;
use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Review
 *
 * @property int $id
 * @property int $site_id
 * @property string $advantages
 * @property string $disadvantages
 * @property string $comment
 * @property int $create_user_id
 * @property int $rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User|null $create_user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Review onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereAdvantages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereDisadvantages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Review withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Review withoutTrashed()
 * @mixin \Eloquent
 * @property int $all_text_length
 * @property-read \App\Site $site
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereAllTextLength($value)
 * @property int $rating
 * @property int $rate_up
 * @property int $rate_down
 * @property int $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ReviewRating[] $authUserRatings
 * @property-read int|null $auth_user_votes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ReviewRating[] $ratings
 * @property-read int|null $ratings_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereVoteDown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereVoteUp($value)
 * @property-read int|null $auth_user_ratings_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereRateDown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereRateUp($value)
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Model void()
 */
class Review extends Model
{
    use SoftDeletes;
    use UserCreate;
    use UpAndDownRateableTrait;

    public const RATEABLE_MODEL = 'App\ReviewRating';

    protected $fillable = [
        'advantages',
        'disadvantages',
        'comment',
        'rate'
    ];

    public function scopeAny($query)
    {
        return $query->withTrashed();
    }

    public function site()
    {
        return $this->belongsTo('App\Site')
            ->any();
    }

    public function setAdvantagesAttribute($value)
    {
        $this->attributes['advantages'] = trim($value);
        $this->attributes['all_text_length'] = $this->getAllTextLength();
    }

    public function setDisadvantagesAttribute($value)
    {
        $this->attributes['disadvantages'] = trim($value);
        $this->attributes['all_text_length'] = $this->getAllTextLength();
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = trim($value);
        $this->attributes['all_text_length'] = $this->getAllTextLength();
    }

    public function getAllTextLength()
    {
        $text = preg_replace('/([[:space:]]+)/', '', $this->advantages);
        $text .= preg_replace('/([[:space:]]+)/', '', $this->disadvantages);
        $text .= preg_replace('/([[:space:]]+)/', '', $this->comment);

        return mb_strlen($text);
    }

    public function updateChildrenCount()
    {
        $this->children_count = $this->comments()->roots()->count();
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function getRedirectToUrl()
    {
        return route('reviews.go_to', $this);
    }

    public function getAdvantagesHtml()
    {
        return $this->replaceNewLinesToBreakLines($this->advantages);
    }

    public function getDisadvantagesHtml()
    {
        return $this->replaceNewLinesToBreakLines($this->disadvantages);
    }

    public function getCommentHtml()
    {
        return $this->replaceNewLinesToBreakLines($this->comment);
    }

    public function replaceNewLinesToBreakLines($value)
    {
        return str_replace("\n", '<br />', $value);
    }

    public function isCreatorIsSiteOwner() :bool
    {
        return $this->create_user->is($this->site->userOwner);
    }
}
