<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Litlife\Url\Url;

/**
 * App\ProofOwnership
 *
 * @property int $id
 * @property int $site_owner_id
 * @property string $dns_code
 * @property string $file_code
 * @property string $file_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\SiteOwner $siteOwner
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\ProofOwnership onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership whereDnsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership whereDnsCodes($column, $dnsCodes)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership whereFileCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership whereSiteOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProofOwnership whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProofOwnership withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\ProofOwnership withoutTrashed()
 * @mixin \Eloquent
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Model void()
 */
class ProofOwnership extends Model
{
    use SoftDeletes;

    public function siteOwner()
    {
        return $this->belongsTo('App\SiteOwner')->withTrashed();
    }

    public function getFileUrl()
    {
        $site = $this->siteOwner->site;

        return (string)Url::fromString('')
            ->withHost($site->domain)
            ->withScheme('http')
            ->withDirname('/'.$this->file_path);
    }

    public function scopeWhereDnsCodes($query, $column, $dnsCodes)
    {
        if ($dnsCodes instanceof Collection)
        {
            $dnsCodes = $dnsCodes->all();
        }

        if (is_array($dnsCodes))
            return $query->whereIn('dns_code', $dnsCodes);
        else
            return $query->where('dns_code', $dnsCodes);
    }
}
