<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\UserInvitation
 *
 * @property int $id
 * @property string $email
 * @property string $token
 * @property string|null $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserInvitation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserInvitation whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserInvitation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserInvitation whereUsedAt($value)
 * @mixin \Eloquent
 */
class UserInvitation extends Model
{
    public $fillable = [
        'email'
    ];

    public function setEmailAttribute($email)
    {
        $email = trim($email);

        $this->attributes['email'] = mb_strtolower($email);
    }

    public function isUsed() :bool
    {
        return (boolean)$this->used_at;
    }

    public function used()
    {
        $this->used_at = now();
    }

    public function scopeWhereToken($query, $token)
    {
        return $query->where('token', trim($token));
    }
}
