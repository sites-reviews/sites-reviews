<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PasswordReset
 *
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $used_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PasswordReset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PasswordReset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PasswordReset whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PasswordReset whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PasswordReset whereUserId($value)
 * @mixin \Eloquent
 */
class PasswordReset extends Model
{
    public const UPDATED_AT = null;

    public $fillable = [
        'email'
    ];

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = trim(mb_strtolower($value));
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function isUsed()
    {
        return (boolean)$this->used_at;
    }
}
