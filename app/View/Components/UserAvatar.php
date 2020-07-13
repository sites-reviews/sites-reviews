<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UserAvatar extends Component
{
    public $user;
    public $width;
    public $height;
    public $quality;
    public $quality2x;
    public $quality3x;
    public $defaultUrl;
    public $url;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($user, $width, $height, $quality = 90, $url = 1)
    {
        $this->user = $user;
        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
        $this->quality2x = $this->quality - 5;
        $this->quality3x = $this->quality - 10;
        $this->defaultUrl = '/img/no-user-image-available.png';
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        if (empty($this->user))
            return <<<'blade'
<img class="lazyload img-fluid rounded-circle d-block" 
srcset="{{ $defaultUrl }}?w={{ $width*2 }}&h={{ $height*2 }} 2x, {{ $defaultUrl }}?w={{ $width*3 }}&h={{ $height*3 }} 3x"
data-src="{{ $defaultUrl }}?w={{ $width }}&h={{ $height }}"/>
blade;

        if ($this->user->trashed())
            return <<<'blade'
<img class="lazyload img-fluid rounded-circle d-block" 
srcset="{{ $defaultUrl }}?w={{ $width*2 }}&h={{ $height*2 }} 2x, {{ $defaultUrl }}?w={{ $width*3 }}&h={{ $height*3 }} 3x"
data-src="{{ $defaultUrl }}?w={{ $width }}&h={{ $height }}"/>
blade;

        if (empty($this->user->avatarPreview)) {
            $output = '';

            if ($this->url) {
                $output .= <<<'blade'
<a title="{{ $user->userName }}" href="{{ route('users.show', $user) }}" class="text-decoration-none d-block text-center">
blade;
            }

            $output .= <<<'blade'
<img class="lazyload img-fluid rounded-circle" 
srcset="{{ $defaultUrl }}?w={{ $width*2 }}&h={{ $height*2 }} 2x, {{ $defaultUrl }}?w={{ $width*3 }}&h={{ $height*3 }} 3x"
data-src="{{ $defaultUrl }}?w={{ $width }}&h={{ $height }}"/>
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
<a title="{{ $user->userName }}" href="{{ route('users.avatar', $user) }}" class="text-decoration-none d-block  text-center" style="width:{{ $width }}px; height: {{ $height }}px;">
blade;
            }

            $output .= <<<'blade'
<img class="lazyload img-fluid rounded-circle" itemprop="image" alt="{{ $user->userName }}" 
srcset="{{ $user->avatarPreview->fullUrlMaxSize($width * 2, $height * 2, $quality2x) }} 2x, {{ $user->avatarPreview->fullUrlMaxSize($width * 3, $height * 3, $quality3x) }} 3x"
data-src="{{ $user->avatarPreview->fullUrlMaxSize($width, $height, $quality) }}"/>
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
