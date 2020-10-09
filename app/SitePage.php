<?php

namespace App;

/**
 * App\SitePage
 *
 * @property int $id
 * @property int $site_id site_pages.site_id
 * @property string $content site_pages.content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Site $site
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SitePage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SitePage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SitePage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SitePage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SitePage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SitePage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SitePage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SitePage whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SitePage whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $search_for_new_domains_is_completed_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SitePage whereSearchForNewDomainsIsCompletedAt($value)
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Model void()
 */
class SitePage extends Model
{
    private $dom;
    private $xpath;
    private $head;
    private $body;

    public $attributes = [
        'content' => ''
    ];

    public $timestamps = [
        'search_for_new_domains_is_completed_at'
    ];

    public function site()
    {
        return $this->belongsTo('App\Site', 'site_id', 'id');
    }

    public function setContentAttribute($value)
    {
        mb_substitute_character(0x20);

        $value = trim($value);

        if (preg_match('/^(?:[0-9]+)\,(?:[0-9]+)\ (?:[A-z]{1})B\ \((?:[0-9]+)\,(?:[0-9]+)\ (?:[A-z]{1})B\ loaded\)(.*)/iu', $value, $matches)) {
            $value = $matches[1];
        }

        $result = mb_check_encoding($value, "UTF-8");

        if (!$result) {
            try {
                $value = mb_convert_encoding($value, 'utf-8', 'auto');
            } catch (\ErrorException $exception) {
                $value = utf8_encode($value);
            }
        }

        $value = mb_convert_encoding($value, 'utf-8', 'auto');

        $this->attributes['content'] = $value;
    }

    public function dom(): \DOMDocument
    {
        if (!isset($this->dom)) {
            $this->dom = new \DOMDocument();
            @$this->dom->loadHTML('<?xml encoding="utf-8" ?>' . $this->content);
            $this->dom->encoding = 'utf-8';
        }

        return $this->dom;
    }

    public function xpath(): \DOMXPath
    {
        if (!isset($this->xpath)) {
            $this->xpath = new \DOMXPath($this->dom());
        }

        return $this->xpath;
    }

    public function head()
    {
        return $this->xpath()->query("//head")->item(0);
    }

    public function body()
    {
        return $this->xpath()->query("//body")->item(0);
    }

    public function getMetaData(): array
    {
        $metaData = [];

        foreach ($this->head()->getElementsByTagName('meta') as $metaNode) {
            if ($metaNode->hasAttribute('property')) {
                $content = '';

                if ($metaNode->hasAttribute('content')) {
                    $content = trim($metaNode->getAttribute('content'));
                }

                $metaData[$metaNode->getAttribute('property')] = $content;
            }

            if ($metaNode->hasAttribute('name')) {
                $content = '';

                if ($metaNode->hasAttribute('content')) {
                    $content = trim($metaNode->getAttribute('content'));
                }

                $metaData[$metaNode->getAttribute('name')] = $content;
            }
        }

        return $metaData;
    }

    public function getTitleValue(): string
    {
        $title = '';

        $titleNode = $this->head()
            ->getElementsByTagName('title')
            ->item(0);

        if (!empty($titleNode)) {
            $title = $titleNode->nodeValue;
        }

        return trim($title);
    }
}
