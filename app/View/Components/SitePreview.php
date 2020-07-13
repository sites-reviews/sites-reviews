<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SitePreview extends Component
{
    public $site;
    public $width;
    public $height;
    public $quality;
    public $quality2x;
    public $quality3x;
    public $defaultUrl;
    public $url;
    public $showImageUpdateSoonText;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($site, $width, $height, $quality = 90, $url = 1, $showImageUpdateSoonText = true)
    {
        $this->site = $site;
        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
        $this->quality2x = $this->quality - 5;
        $this->quality3x = $this->quality - 10;
        $this->defaultUrl = '/img/default.png';
        $this->url = $url;
        $this->showImageUpdateSoonText = $showImageUpdateSoonText;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        if (empty($this->site))
            return <<<'blade'
<img class="lazyload img-fluid d-block" 
srcset="{{ $defaultUrl }}?w={{ $width*2 }}&h={{ $height*2 }} 2x, {{ $defaultUrl }}?w={{ $width*3 }}&h={{ $height*3 }} 3x"
data-src="{{ $defaultUrl }}?w={{ $width }}&h={{ $height }}"/>
blade;

        if ($this->site->trashed())
            return <<<'blade'
<img class="lazyload img-fluid d-block" 
srcset="{{ $defaultUrl }}?w={{ $width*2 }}&h={{ $height*2 }} 2x, {{ $defaultUrl }}?w={{ $width*3 }}&h={{ $height*3 }} 3x"
data-src="{{ $defaultUrl }}?w={{ $width }}&h={{ $height }}"/>
blade;

        if (empty($this->site->preview)) {
            $output = '';

            if ($this->url) {
                $output .= <<<'blade'
<a title="{{ $site->siteName }}" href="{{ route('sites.show', $site) }}" class="text-decoration-none d-block">
blade;
            }

            $output .= <<<'blade'
<div class="border p-3 d-flex flex-column align-items-center" style="width:{{ $width }}px;">
<div class="h1"><i class="fas fa-camera text-secondary"></i></div>
blade;
            if ($this->showImageUpdateSoonText)
            {
                $output .= <<<'blade'
<div>{{ __('Site image will be updated soon') }}</div>
blade;
            }

            $output .= <<<'blade'
</div>
blade;
            if ($this->url) {
                $output .= <<<'blade'
</a>
blade;
            }

            return $output;

        } else {

            $output = '';

            if ($this->url) {
                $output .= <<<'blade'
<a title="{{ $site->siteName }}" href="{{ route('sites.show', $site) }}" class="text-decoration-none d-block" style="width:{{ $width }}px; height: {{ $height }}px;">
blade;
            }

            $output .= <<<'blade'
<img class="lazyload img-fluid" itemprop="image" alt="{{ $site->siteName }}" 
srcset="{{ $site->preview->fullUrlMaxSize($width * 2, $height * 2, $quality2x) }} 2x, {{ $site->preview->fullUrlMaxSize($width * 3, $height * 3, $quality3x) }} 3x"
data-src="{{ $site->preview->fullUrlMaxSize($width, $height, $quality) }}"/>
blade;
            if ($this->url) {
                $output .= <<<'blade'
</a>
blade;
            }

            return $output;
        }
    }
}
