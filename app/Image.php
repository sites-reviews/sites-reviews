<?php

namespace App;

use App\Traits\ImageableTrait;
use App\Traits\ImageResizable;
use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Litlife\IdDirname\IdDirname;
use Litlife\Url\Url;

/**
 * App\Image
 *
 * @property int $id
 * @property string $name
 * @property string $storage
 * @property int|null $create_user_id
 * @property int $filesize
 * @property string $type
 * @property string|null $dirname
 * @property string|null $phash
 * @property string|null $sha256_hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User|null $create_user
 * @property-read mixed $full_url200x200
 * @property-read mixed $full_url50x50
 * @property-read mixed $full_url90x90
 * @property-read mixed $full_url
 * @property-read mixed $url
 * @property-read mixed $full_url_sized
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Image[] $images
 * @property-read int|null $images_count
 * @property-write mixed $max_height
 * @property-write mixed $max_width
 * @property-write mixed $quality
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image md5Hash($hash)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Image onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image pHash($hash)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image setSize($height)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image sha256Hash($hash)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereDirname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereFilesize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image wherePhash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereSha256Hash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereStorage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Image withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Image withoutTrashed()
 * @mixin \Eloquent
 */
class Image extends Model
{
    use SoftDeletes;
    use ImageableTrait;
    use ImageResizable;
    use UserCreate;

    public $folder = '_i';
    public $img;

    protected $appends = ['fullUrlSized', 'url'];

    public function scopeSetSize($width, $height)
    {
        $this->maxWidth = $width;
        $this->maxHeight = $height;
    }

    public function scopeSha256Hash($query, $hash)
    {
        return $query->where('sha256_hash', $hash);
    }

    public function scopeMd5Hash($query, $hash)
    {
        return $query->where('md5', $hash);
    }

    public function scopePHash($query, $hash)
    {
        return $query->where('phash', $hash);
    }
    /*
    public function getWidthAttribute($query)
    {

    }

    public function getHeightAttribute($query)
    {

    }
    */

    public function getDirname()
    {
        $idDirname = new IdDirname($this->id);

        $url = (new Url)->withDirname('images/' . implode('/', $idDirname->getDirnameArrayEncoded()));

        return trim($url->getPath(), '/');
    }
}
