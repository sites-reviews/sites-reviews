@extends('layouts.app')

@push('after_scripts')
    <script src="{{ mix('js/sites.show.js', 'assets') }}" defer></script>
@endpush

@section('content')

    <div itemprop="mainEntity" itemscope itemtype="http://schema.org/Organization">
        <div class="card mb-3">
            <div class="card-body d-flex flex-column flex-sm-row ">

                <div class="flex-shrink-1 text-center mr-3 mb-3" style="width:220px;">
                    <a href="{{ $site->getUrlWithUtm() }}" target="_blank">
                        <x-site-preview :site="$site" width="200" height="200" url="0"/>
                    </a>
                </div>

                <div class="w-100">
                    <a href="{{ $site->getUrlWithUtm() }}" target="_blank">
                        <h2 itemprop="name">{{ $site->title }}</h2>
                    </a>
                    <h6 itemprop="description">{{ $site->description }}</h6>

                    <div class="">
                        <a href="{{ $site->getUrlWithUtm() }}" target="_blank" itemprop="url">{{ $site->domain }}</a>
                    </div>

                    @if ($site->rating > 0 and $site->number_of_reviews > 0)
                        <div itemprop="aggregateRating"
                             itemscope itemtype="http://schema.org/AggregateRating">
                            <meta itemprop="reviewCount" content="{{ $site->number_of_reviews }}">
                            <meta itemprop="worstRating" content="1">
                            <meta itemprop="bestRating" content="5">
                            <meta itemprop="ratingValue" content="{{ $site->rating }}">
                        </div>
                    @endif

                    <div class="d-flex flex-row flex-column flex-md-row">

                        <div class="d-flex align-items-center flex-wrap">
                            <div class="h3 mr-3">
                                <x-site-rating :rating="$site->rating"/>
                            </div>

                            <div class="mr-3 text-nowrap">
                                <span class="h3">{{ $site->rating }}</span>/<span class="small">5</span>
                            </div>

                            <div class="text-nowrap">
                                {{ mb_ucfirst(trans_choice('site.reviews', $site->number_of_reviews)) }}:
                                {{ $site->number_of_reviews }}
                            </div>

                        </div>

                        <div class="ml-md-3 mt-md-0 mt-2">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#shareRatingModal">
                                <i class="fas fa-share-alt"></i> &nbsp; {{ __('Share a rating') }}
                            </button>

                            @empty ($owner)
                                <a class="btn btn-primary" href="{{ route('sites.verification.request', ['site' => $site]) }}">
                                    {{ __('This is my site') }}
                                </a>
                            @endempty

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
                <h5>{{ __('Your review') }}:</h5>

                @include('site.review.create_form', ['create_user' => auth()->user()])

                <div class="mb-3"></div>
            @endif
        @endauth

        @guest
            <h5>{{ __('Your review') }}:</h5>

            @include('site.review.create_form', ['create_user' => null])

            <div class="mb-3"></div>
        @endguest

        <div class="d-flex flex-row align-items-center pb-2 overflow-auto">
            <h5 class="w-100 text-nowrap">{{ __('Reviews') }} <span class="badge badge-info">{{ $site->number_of_reviews }}</span></h5>

            <div class="ml-4 d-flex flex-sm-shrink-1 small">
                <div class="text-body mr-3">{{ __('Sort') }}:</div>
                <a href="{{ route('sites.show', ['site' => $site, 'reviews_order_by' => 'latest']) }}"
                   class="mr-3 text-nowrap @if ($reviews_order_by == 'latest') text-primary @else text-secondary @endif">
                    {{ __('by date') }}
                </a>
                <a href="{{ route('sites.show', ['site' => $site, 'reviews_order_by' => 'rating_desc']) }}"
                   class="mr-3 text-nowrap @if ($reviews_order_by == 'rating_desc') text-primary @else text-secondary @endif">
                    {{ __('by rating') }}
                </a>
            </div>
        </div>

        @if ($reviews->count() > 0)

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
