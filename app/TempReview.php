<?php

namespace App;

use App\Traits\HasEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\TempReview
 *
 * @property int $id
 * @property int $site_id ID сайта
 * @property string $advantages Преймущества
 * @property string $disadvantages Недостатки
 * @property string $comment Дополнительный комментарий
 * @property int $rate Оценка
 * @property string $email Адрес почтового ящика
 * @property string $token Уникальный ключ
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Site $site
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview newQuery()
 * @method static \Illuminate\Database\Query\Builder|TempReview onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview query()
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereAdvantages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereDisadvantages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereEmailsIn($emails)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempReview whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|TempReview withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TempReview withoutTrashed()
 * @mixin \Eloquent
 */
class TempReview extends Model
{
    use HasFactory;
    use HasEmail;
    use SoftDeletes;

    protected $fillable = [
        'advantages',
        'disadvantages',
        'comment',
        'rate',
        'email'
    ];

    public function setAdvantagesAttribute($value)
    {
        $this->attributes['advantages'] = trim($value);
    }

    public function setDisadvantagesAttribute($value)
    {
        $this->attributes['disadvantages'] = trim($value);
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = trim($value);
    }

    public function site()
    {
        return $this->belongsTo('App\Site')
            ->any();
    }
}
