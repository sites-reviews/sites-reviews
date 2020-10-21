<?php

namespace App;

use App\Enums\StatusEnum;
use App\Traits\CheckedItems;
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
 * @method static \Illuminate\Database\Eloquent\Builder|Review any()
 * @property int|null $status
 * @property string|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read \App\User|null $status_changed_user
 * @method static \Illuminate\Database\Eloquent\Builder|Review accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Review acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Review acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Review acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Review acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Review acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Review checked()
 * @method static \Illuminate\Database\Eloquent\Builder|Review checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Review checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Review onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Review onlyChecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Review orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|Review orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Review private()
 * @method static \Illuminate\Database\Eloquent\Builder|Review sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Review unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Review unchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|Review withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Review withoutCheckedScope()
 */
class Review extends Model
{
    use SoftDeletes;
    use UserCreate;
    use UpAndDownRateableTrait;
    use CheckedItems;

    public const RATEABLE_MODEL = 'App\ReviewRating';

    public $attributes = [
        'status' => StatusEnum::Accepted
    ];

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

    public function getAnchorName()
    {
        return strtolower('review'.$this->id);
    }

    public function getGoToUrl()
    {
        return route('reviews.go_to', ['review' => $this]);
    }
}
