<?php

namespace App;

/**
 * App\UserNotificationSetting
 *
 * @property int $id
 * @property int $user_id
 * @property bool $email_response_to_my_review
 * @property bool $db_response_to_my_review
 * @property bool $db_when_review_was_liked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting whereDbResponseToMyReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting whereDbWhenReviewWasLiked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting whereEmailResponseToMyReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting whereUserId($value)
 * @mixin \Eloquent
 * @property bool $email_response_to_my_comment
 * @property bool $db_response_to_my_comment
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting whereDbResponseToMyComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting whereEmailResponseToMyComment($value)
 * @property bool $db_when_comment_was_liked Когда мой комментарий понравился
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserNotificationSetting whereDbWhenCommentWasLiked($value)
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Model void()
 */
class UserNotificationSetting extends Model
{
    public $attributes = [
        'email_response_to_my_review' => true,
        'email_response_to_my_comment' => true,
        'db_response_to_my_review' => true,
        'db_when_review_was_liked' => true,
        'db_when_comment_was_liked' => true,
        'db_response_to_my_comment' => true
    ];

    public $fillable = [
        'email_response_to_my_review',
        'email_response_to_my_comment',
        'db_response_to_my_review',
        'db_when_review_was_liked',
        'db_when_comment_was_liked',
        'db_response_to_my_comment'
    ];

    public function getEmailNotifications()
    {
        return [
            'email_response_to_my_review',
            'email_response_to_my_comment'
        ];
    }

    public function getDBNotifications()
    {
        return [
            'db_response_to_my_review',
            'db_when_review_was_liked',
            'db_response_to_my_comment',
            'db_when_comment_was_liked'
        ];
    }


    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
