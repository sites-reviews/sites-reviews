<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PossibleDomain
 *
 * @property int $id
 * @property string $domain
 * @property string|null $handeled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PossibleDomain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PossibleDomain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PossibleDomain query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PossibleDomain unhandeled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PossibleDomain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PossibleDomain whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PossibleDomain whereHandeledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PossibleDomain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PossibleDomain whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PossibleDomain extends Model
{
    public function setDomainAttribute($value)
    {
        $this->attributes['domain'] = $this->domainVal($value);
    }

    public function domainVal($value)
    {
        $value = filter_var($value, FILTER_SANITIZE_URL);
        $value = trim($value);
        $value = mb_strtolower($value);

        if (preg_match('/^(?:www\.?)(.*)$/iu', $value, $matches)) {
            $value = $matches[1];
        }

        $value = trim($value, '.');

        return $value;
    }

    public function scopeWhereDomain($query, $value)
    {
        return $query->where('domain', $this->domainVal($value));
    }

    public function scopeUnhandeled($query)
    {
        return $query->whereNull('handeled_at');
    }
}
