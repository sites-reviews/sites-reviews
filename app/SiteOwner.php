<?php

namespace App;

use App\Traits\CheckedItems;
use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\SiteOwner
 *
 * @property int $id
 * @property int $create_user_id
 * @property int $site_id
 * @property int|null $status
 * @property string|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User $create_user
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProofOwnership[] $proof
 * @property-read int|null $proof_count
 * @property-read \App\Site $site
 * @property-read \App\User|null $status_changed_user
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner checked()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner onlyChecked()
 * @method static \Illuminate\Database\Query\Builder|\App\SiteOwner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner private()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner unchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SiteOwner withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SiteOwner withoutCheckedScope()
 * @method static \Illuminate\Database\Query\Builder|\App\SiteOwner withoutTrashed()
 * @mixin \Eloquent
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Model void()
 */
class SiteOwner extends Model
{
    use UserCreate;
    use SoftDeletes;
    use CheckedItems;

    public function site()
    {
        return $this->belongsTo('App\Site')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }

    public function proof()
    {
        return $this->hasMany('App\ProofOwnership');
    }
}
