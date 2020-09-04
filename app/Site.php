<?php

namespace App;

use App\Enums\SiteHowAddedEnum;
use App\Library\StarFullness;
use App\Traits\UserCreate;
use Eloquent;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Imagick;
use ImagickDraw;
use ImagickPixel;
use Litlife\Url\Url;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * App\Site
 *
 * @property int $id
 * @property string $domain
 * @property string $title
 * @property string $description
 * @property int|null $create_user_id
 * @property int|null $preview_image_id
 * @property float|null $rating
 * @property int $number_of_views
 * @property int $number_of_reviews
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $create_user
 * @property-read Image|null $preview
 * @property-read Collection|Review[] $reviews
 * @property-read int|null $reviews_count
 * @method static Builder|Site newModelQuery()
 * @method static Builder|Site newQuery()
 * @method static \Illuminate\Database\Query\Builder|Site onlyTrashed()
 * @method static Builder|Site query()
 * @method static Builder|Site whereCreateUserId($value)
 * @method static Builder|Site whereCreatedAt($value)
 * @method static Builder|Site whereCreator(User $user)
 * @method static Builder|Site whereDeletedAt($value)
 * @method static Builder|Site whereDescription($value)
 * @method static Builder|Site whereDomain($value)
 * @method static Builder|Site whereId($value)
 * @method static Builder|Site whereNumberOfReviews($value)
 * @method static Builder|Site whereNumberOfViews($value)
 * @method static Builder|Site wherePreviewImageId($value)
 * @method static Builder|Site whereRating($value)
 * @method static Builder|Site whereTitle($value)
 * @method static Builder|Site whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Site withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Site withoutTrashed()
 * @mixin Eloquent
 * @property bool $update_the_preview
 * @method static Builder|Site url($url)
 * @method static Builder|Site whereUpdateThePreview($value)
 * @property bool $update_the_page
 * @property array|null $meta_data
 * @property-read Collection|SiteOwner[] $siteOwners
 * @property-read int|null $site_owners_count
 * @method static Builder|Site fulltextSearch($searchText)
 * @method static Builder|Site whereMetaData($value)
 * @method static Builder|Site whereUpdateTheMetadata($value)
 * @property string|null $latest_rating_changes_at Дата последнего изменения рейтинга
 * @property-read \App\SitePage $page
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site orWhereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereLatestRatingChangesAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereUpdateThePage($value)
 * @property int|null $number_of_attempts_update_the_preview Количество попыток обновить превью сайта
 * @property int|null $number_of_attempts_update_the_page Количество попыток обновить страницу
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereNumberOfAttemptsUpdateThePage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereNumberOfAttemptsUpdateThePreview($value)
 * @property int|null $how_added Как был добавлен сайт (вручную, через расширение и т.д.)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site orderManuallyAddedFirst()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site titleILike($searchText)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereHowAdded($value)
 */
class Site extends Model
{
    use SoftDeletes;
    use UserCreate;

    protected $casts = [
        'meta_data' => 'array',
        'create_user_id' => 'integer',
        'number_of_views' => 'integer',
        'number_of_reviews' => 'integer',
        'number_of_attempts_update_the_preview' => 'integer',
        'number_of_attempts_update_the_page' => 'integer',
        'update_the_preview' => 'boolean',
        'update_the_page' => 'boolean',
        'available' => 'boolean'
    ];

    public $timestamps = [
        'created_at',
        'updated_at',
        'latest_rating_changes_at',
        'deleted_at',
        'available_check_at'
    ];

    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed $value
     * @param string|null $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('domain', $this->domainVal($value))
            ->firstOrFail();
    }

    public function getRouteKeyName()
    {
        return 'domain';
    }

    public function scopeWhereDomain($query, $value)
    {
        return $query->where('domain', $this->domainVal($value));
    }

    public function scopeOrWhereDomain($query, $value)
    {
        return $query->orWhere('domain', $this->domainVal($value));
    }

    public function preview()
    {
        return $this->belongsTo('App\Image', 'preview_image_id', 'id');
    }

    public function page()
    {
        return $this->hasOne('App\SitePage', 'site_id', 'id')
            ->withDefault();
    }

    public function reviews()
    {
        return $this->hasMany('App\Review');
    }

    public function updateNumberOfReviews()
    {
        $this->number_of_reviews = $this->reviews()->count();
    }

    public function getUrl(): Url
    {
        return Url::fromString('')
            ->withScheme('http')
            ->withHost($this->domain);
    }

    public function updateRating()
    {
        $this->rating = $this->reviews()->avg('rate');
        $this->latest_rating_changes_at = now();
    }

    public function setRatingAttribute($value)
    {
        if ($value > 5)
            $value = 5;

        if ($value < 0)
            $value = 0;

        $this->attributes['rating'] = round($value, 2);
    }

    public function getRatingAttribute($value)
    {
        return round($value, 2);
    }

    public function siteOwners()
    {
        return $this->hasMany('App\SiteOwner');
    }

    public function scopeFulltextSearch($query, $searchText)
    {
        $searchText = preg_replace("/[\"\']+/", '', $searchText);

        return $query->where('title', 'ilike', '%' . $searchText . '%');
    }

    public function scopeTitleILike($query, $searchText)
    {
        $Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

        $s = '';

        if ($Ar) {
            $s = "to_tsvector('english', \"title\") ";
            $s .= " @@ to_tsquery(quote_literal(quote_literal(?)) || ':*')";
            return $query->whereRaw($s, implode('+', $Ar));
        }
        return $query;
    }

    public function setTitleAttribute($value)
    {
        $value = trim($value);

        if (preg_match('/^(?:www\.?)(.*)$/iu', $value, $matches)) {
            $value = $matches[1];
        }

        $this->attributes['title'] = mb_ucfirst($value);
    }

    public function setDomainAttribute($value)
    {
        $this->attributes['domain'] = $this->domainVal($value);
    }

    public function getDomainAttribute($value)
    {
        return mb_strtolower($value);
    }

    public function buttonHtmlCode()
    {
        return '<a href="' . route('sites.show', $this) . '">' . "\n" .
            '<img srcset="' . route('sites.rating.image', ['site' => $this, 'size' => '2x']) . ' 2x, ' . route('sites.rating.image', ['site' => $this, 'size' => '3x']) . ' 3x" ' .
            'data-src="' . route('sites.rating.image', ['site' => $this, 'size' => '1x']) . '" ' .
            'width="88" height="31" border="0" alt="' . $this->buttonImageAltText() . '" />' . "\n" .
            '</a>';
    }

    public function buttonBBCode()
    {
        return '[url=' . route('sites.show', $this) . ']' . "\n" .
            '[img]' . route('sites.rating.image', ['site' => $this, 'size' => '1x']) . '[/img]' . "\n" .
            '[/url]';
    }

    public function buttonImageAltText()
    {
        return __('Rating and reviews of the site') . ' ' . $this->domain;
    }

    public function getNumberOfReviewsHumanReadable()
    {
        $value = $this->number_of_reviews;

        if ($value < 1000) {
            return $value;
        } elseif ($value >= 1000 and $value < 1000000) {
            return round($value / 1000, 1) . 'K';
        } elseif ($value >= 1000000) {
            return round($value / 1000000, 1) . 'M';
        }
    }

    public function getRatingForButton()
    {
        return number_format($this->rating, 1);
    }

    public function getRatingImageBlob($size = '1x', $cache = true)
    {
        if ($cache)
            $blob = Cache::get($this->id . ':ri:' . $size);

        if (empty($blob)) {
            $rating = $this->rating;

            $siteFullness = new StarFullness();
            $siteFullness->setRate($rating);

            $width = 100;
            $height = 100;
            $leftMargin = 40;
            $pixelsBetween = 10;
            $topMargin = 40;
            $rightMargin = 40;
            $fontSize = 90;

            $solidStar = '<?xml version="1.0" ?><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" class="svg-inline--fa fa-star fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="' . $siteFullness->getColor() . '" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>';
            $regularStar = '<?xml version="1.0" ?><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="star" class="svg-inline--fa fa-star fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="' . $siteFullness->getColor() . '" d="M528.1 171.5L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6zM388.6 312.3l23.7 138.4L288 385.4l-124.3 65.3 23.7-138.4-100.6-98 139-20.2 62.2-126 62.2 126 139 20.2-100.6 98z"></path></svg>';
            $halfStar = '<?xml version="1.0" ?><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star-half-alt" class="svg-inline--fa fa-star-half-alt fa-w-17" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 536 512"><path fill="' . $siteFullness->getColor() . '" d="M508.55 171.51L362.18 150.2 296.77 17.81C290.89 5.98 279.42 0 267.95 0c-11.4 0-22.79 5.9-28.69 17.81l-65.43 132.38-146.38 21.29c-26.25 3.8-36.77 36.09-17.74 54.59l105.89 103-25.06 145.48C86.98 495.33 103.57 512 122.15 512c4.93 0 10-1.17 14.87-3.75l130.95-68.68 130.94 68.7c4.86 2.55 9.92 3.71 14.83 3.71 18.6 0 35.22-16.61 31.66-37.4l-25.03-145.49 105.91-102.98c19.04-18.5 8.52-50.8-17.73-54.6zm-121.74 123.2l-18.12 17.62 4.28 24.88 19.52 113.45-102.13-53.59-22.38-11.74.03-317.19 51.03 103.29 11.18 22.63 25.01 3.64 114.23 16.63-82.65 80.38z"></path></svg>';

            $solidStarImagick = new Imagick();
            $solidStarImagick->readImageBlob($solidStar);
            $solidStarImagick->resizeImage(1000, 500, Imagick::FILTER_SINC, true, true);
            $solidStarImagick->cropImage(523, 500, 20, 0);
            $solidStarImagick->resizeImage($width, $height, Imagick::FILTER_SINC, true, true);

            $regularStarImagick = new Imagick();
            $regularStarImagick->readImageBlob($regularStar);
            $regularStarImagick->resizeImage(1000, 500, Imagick::FILTER_SINC, true, true);
            $regularStarImagick->cropImage(523, 500, 20, 0);
            $regularStarImagick->resizeImage($width, $height, Imagick::FILTER_SINC, true, true);

            $halfStarImagick = new Imagick();
            $halfStarImagick->readImageBlob($halfStar);
            $halfStarImagick->resizeImage(1000, 500, Imagick::FILTER_SINC, true, true);
            $halfStarImagick->resizeImage($width, $height, Imagick::FILTER_SINC, true, true);

            $imagick = new Imagick();
            $imagick->newImage(880, 310, new ImagickPixel('white'));
            $imagick->setImageFormat('png');

            foreach ($siteFullness->getArray() as $number => $value) {
                switch ($value) {
                    case 'filled':
                        $star = $solidStarImagick->getimage();
                        break;
                    case 'half':
                        $star = $halfStarImagick->getimage();
                        break;
                    case 'empty':
                        $star = $regularStarImagick->getimage();
                        break;
                }

                $imagick->compositeimage($star, Imagick::COMPOSITE_COPY, $leftMargin + (($width + $pixelsBetween) * ($number - 1)), $topMargin);
            }

            $imagick->setImageFormat('png');

            $draw = new ImagickDraw();
            $draw->setFontSize($fontSize);
            $draw->setFillColor('black');
            $imagick->annotateImage($draw, $leftMargin, 270, 0, config('app.name'));

            $numberOfReviewsDraw = new ImagickDraw();
            $numberOfReviewsDraw->setFontSize($fontSize);
            $numberOfReviewsDraw->setFillColor('black');
            $numberOfReviewsDraw->setTextAlignment(Imagick::ALIGN_RIGHT);
            $numberOfReviewsDraw->annotation(880 - $rightMargin, 270, $this->getNumberOfReviewsHumanReadable());
            $imagick->drawImage($numberOfReviewsDraw);

            $ratingDraw = new ImagickDraw();
            $ratingDraw->setFontSize(110);
            $ratingDraw->setFillColor('black');
            $ratingDraw->setTextAlignment(Imagick::ALIGN_RIGHT);
            $ratingDraw->annotation(880 - $rightMargin, 130, $this->getRatingForButton());
            $imagick->drawImage($ratingDraw);

            $imagick->resizeImage(88 * intval($size), 31 * intval($size), Imagick::FILTER_BLACKMAN, true, true);

            $blob = $imagick->getImageBlob();

            if ($cache)
                Cache::forever($this->id . ':ri:' . $size, $blob);
        }

        return $blob;
    }

    public function clearRatingImageBlob(): bool
    {
        Cache::forget($this->id . ':ri:1x');
        Cache::forget($this->id . ':ri:2x');
        Cache::forget($this->id . ':ri:3x');

        return true;
    }

    public function updateDescriptionFromPage()
    {
        $page = $this->page;

        if ($page->head()) {
            $title = $page->getTitleValue();
            $metaData = $page->getMetaData();

            if (empty($this->description)) {
                if (isset($metaData['description'])) {
                    $description = trim($metaData['description']);

                    if (!empty($description)) {
                        $this->description = $description;
                    }
                }
            }

            if (empty($this->description)) {
                if (isset($metaData['og:description'])) {
                    $description = trim($metaData['og:description']);

                    if (!empty($description)) {
                        $this->description = $description;
                    }
                }
            }

            if (empty($this->description)) {
                if (isset($metaData['twitter:description'])) {
                    $description = trim($metaData['twitter:description']);

                    if (!empty($description)) {
                        $this->description = $description;
                    }
                }
            }

            if (empty($this->description)) {
                if (!empty($title)) {
                    $this->description = $title;
                }
            }
        }

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

    public function isDomainLikeTitle(): bool
    {
        $title = mb_strtolower(trim($this->title));
        $domain = mb_strtolower(trim($this->domain));

        if (preg_match('/^(?:www\.?)(.*)$/iu', $title, $matches)) {
            $title = $matches[1];
        }

        if (preg_match('/^(?:www\.?)(.*)$/iu', $domain, $matches)) {
            $domain = $matches[1];
        }

        return $title == $domain;
    }

    public function getRatingColor($type = 'hex')
    {
        $starFullness = new StarFullness();
        $starFullness->setRate($this->rating);

        if ($type == 'hex')
            return $starFullness->getHexColor();
        elseif ($type == 'rgb')
            return $starFullness->getColor();
    }

    public function isAvailableThroughInternet(Client $client): bool
    {
        $url = Url::fromString('')
            ->withHost($this->domain)
            ->withScheme('http');

        try {
            $response = $client->request(
                'GET',
                (string)$url,
                [
                    'allow_redirects' => true,
                    'connect_timeout' => 5,
                    'read_timeout' => 5,
                    'timeout' => 5,
                    'verify' => false
                ]
            );

            if (!empty($response->getStatusCode()))
                return true;
            else
                return false;

        } catch (TooManyRedirectsException $exception) {

        } catch (ConnectException $exception) {

        } catch (RequestException $exception) {

        } catch (\InvalidArgumentException $exception) {

        }

        return false;
    }

    public function scopeOrderManuallyAddedFirst($query)
    {
        $qs = 'CASE ';
        $qs .= 'WHEN "how_added" = ' . SiteHowAddedEnum::Manually . ' THEN 1 ';
        $qs .= 'WHEN "how_added" = ' . SiteHowAddedEnum::WebExtension . ' THEN 2 ';
        $qs .= 'WHEN "how_added" = ' . SiteHowAddedEnum::PagesScan . ' THEN 3 ';
        $qs .= 'ELSE 10 ';
        $qs .= 'END';

        return $query->orderByRaw($qs);
    }
}
