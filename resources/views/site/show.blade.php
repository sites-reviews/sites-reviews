@extends('layouts.app')

@push('after_scripts')
    <script src="{{ mix('js/sites.show.js', 'assets') }}" defer></script>
@endpush

@section('content')

    <div itemprop="mainEntity" itemscope itemtype="http://schema.org/Organization">
        <div class="card mb-2">
            <div class="card-body d-flex flex-column flex-sm-row ">

                <div class="flex-shrink-1 text-center mr-3 mb-3" style="width:220px;">
                    <x-site-preview :site="$site" width="200" height="200" url="0"/>
                </div>

                <div class="w-100">
                    <a href="{{ $site->getUrl() }}" target="_blank">
                        <h2 itemprop="name">{{ $site->title }}</h2>
                    </a>
                    <h6 itemprop="description">{{ $site->description }}</h6>

                    <div class="">
                        <a href="{{ $site->getUrl() }}" target="_blank" itemprop="url">{{ $site->domain }}</a>
                    </div>

                    {{--
                    <div>
                        <a href="{{ route('sites.verification.request', ['site' => $site]) }}">
                            {{ __('This is my site') }}
                        </a>
                    </div>
--}}
                    <div class="d-flex flex-row flex-column flex-md-row"
                         itemprop="aggregateRating"
                         itemscope itemtype="http://schema.org/AggregateRating">

                        <div class="d-flex align-items-center flex-wrap">
                            <div class="h3 mr-3">
                                <x-site-rating :rating="$site->rating"/>
                            </div>

                            <div class="mr-3  text-nowrap">
                                <meta itemprop="worstRating" content="1">
                                <span class="h3" itemprop="ratingValue">{{ $site->rating }}</span>/<span class="small" itemprop="bestRating">5</span>
                            </div>

                            <div class="text-nowrap ">
                                {{ mb_ucfirst(trans_choice('site.reviews', $site->number_of_reviews)) }}:
                                <span itemprop="reviewCount">{{ $site->number_of_reviews }}</span>
                            </div>

                        </div>

                        <div class="ml-md-3 mt-md-0 mt-2">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#shareRatingModal">
                                <i class="fas fa-share-alt"></i> &nbsp; {{ __('Share a rating') }}
                            </button>

                        </div>

                        <div class="modal" id="shareRatingModal" tabindex="-1" role="dialog" aria-labelledby="shareRatingModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="shareRatingModalLabel">{{ __('Share a rating') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="mb-3">
                                            {!! $site->buttonHtmlCode() !!}
                                        </div>

                                        <div class="accordion" id="accordionCodes">
                                            <div class="card">
                                                <div class="card-header" id="headingOne">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                                                data-target="#collapseHtmlCode"
                                                                aria-expanded="true" aria-controls="collapseHtmlCode">
                                                            {{ __('Code for the blog (Html code)') }}
                                                        </button>
                                                    </h2>
                                                </div>

                                                <div id="collapseHtmlCode" class="collapse show" aria-labelledby="headingOne"
                                                     data-parent="#accordionCodes">
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <textarea class="form-control" id="textareaShareRatingHtmlCode"
                                                                      rows="4">{{ $site->buttonHtmlCode() }}</textarea>
                                                        </div>

                                                        <button class="btn btn-primary" type="button" data-clipboard-action="copy"
                                                                data-clipboard-target="#textareaShareRatingHtmlCode">
                                                            {{ __('Copy to clipboard') }}
                                                        </button>

                                                        <div class="alert alert-success mt-3 mb-0" style="display: none;">
                                                            {{ __('The code was successfully copied to the clipboard') }}
                                                        </div>

                                                        <div class="alert alert-danger mt-3 mb-0" style="display: none;">
                                                            {{ __('Error copying code to clipboard') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card">
                                                <div class="card-header" id="headingTwo">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                                                data-target="#collapseBBCode" aria-expanded="false" aria-controls="collapseBBCode">
                                                            {{ __('Code for the forum (BB code)') }}
                                                        </button>
                                                    </h2>
                                                </div>
                                                <div id="collapseBBCode" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionCodes">
                                                    <div class="card-body">

                                                        <div class="form-group">
                                                            <textarea class="form-control" id="textareaShareRatingBBCode"
                                                                      rows="4">{{ $site->buttonBBCode() }}</textarea>
                                                        </div>

                                                        <button class="btn btn-primary" type="button" data-clipboard-action="copy"
                                                                data-clipboard-target="#textareaShareRatingBBCode">
                                                            {{ __('Copy to clipboard') }}
                                                        </button>

                                                        <div class="alert alert-success mt-3 mb-0" style="display: none;">
                                                            {{ __('The code was successfully copied to the clipboard') }}
                                                        </div>

                                                        <div class="alert alert-danger mt-3 mb-0" style="display: none;">
                                                            {{ __('Error copying code to clipboard') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <h5>{{ __('Reviews') }} <span class="badge badge-info">{{ $site->number_of_reviews }}</span></h5>

        @if ($errors->store_review->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach ($errors->store_review->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @auth
            @if (!empty($authReview))
                <div class="my_review">
                    <x-review :review="$authReview"/>
                </div>
            @else
                @include('site.review.create')
            @endif
        @endauth

        @if ($reviews->count() > 0)

            <div class="py-2 d-flex flex-row">
                <div class="text-body mr-3">{{ __('review.sort') }}:</div>
                <a href="{{ route('sites.show', ['site' => $site, 'reviews_order_by' => 'latest']) }}"
                   class="mr-3 @if ($reviews_order_by == 'latest') text-primary @else text-secondary @endif">
                    {{ __('review.order_by.latest') }}
                </a>
                <a href="{{ route('sites.show', ['site' => $site, 'reviews_order_by' => 'rating_desc']) }}"
                   class="mr-3 @if ($reviews_order_by == 'rating_desc') text-primary @else text-secondary @endif">
                    {{ __('review.order_by.rating_desc') }}
                </a>
            </div>

            <div class="reviews">
                @foreach ($reviews as $review)
                    <x-review :review="$review"/>
                @endforeach
            </div>

            {{ $reviews->links() }}

        @else
            @if (empty($authReview))
                <div class="alert alert-info">
                    {{ __('review.no_reviews_have_been_left_yet') }}
                </div>
            @endif
        @endif
    </div>

@endsection
