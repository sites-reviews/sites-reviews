<div class="btn-group {{ $color_class }}" role="group" aria-label="Basic example" data-value="{{ $rating }}">
    @foreach ($stars as $rate => $star)
        <div class="" data-value="{{ $rate }}" data-name="{{ __('review.rate_names.'.$rate) }}">
            <i class="filled fas fa-star" @if ($star != 'filled') style="display: none;" @endif></i>
            <i class="half fas fa-star-half-alt" @if ($star != 'half') style="display: none;" @endif></i>
            <i class="empty far fa-star" @if ($star != 'empty') style="display: none;" @endif></i>
        </div>
    @endforeach
</div>
