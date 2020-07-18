<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
