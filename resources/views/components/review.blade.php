<div class="review">
    <div class="card mb-2" itemprop="review" itemscope itemtype="http://schema.org/UserReview">
        <div class="card-body d-flex flex-row py-2 px-3">
            <div class="mr-3 text-center flex-shrink-1" style="width:50px;">
                <x-user-avatar :user="$review->create_user" width="50" height="50" quality="90"/>
            </div>
            <div class="w-100">
                <div class="mb-2 d-flex flex-row align-items-center">

                    <div class="d-flex flex-wrap align-items-center">
                        <div class="mr-2" itemprop="author" itemscope itemtype="http://schema.org/Person">
                            <meta itemprop="name" content="{{ $review->create_user->name }}">
                            <meta itemprop="url" content="{{ route('users.show', $review->create_user) }}">

                            <x-user-name :user="$review->create_user"/>

                        </div>
                        @if ($showUserReviewsCount)
                            <div class="mr-2 small text-lowercase text-nowrap">
                                {{ $review->create_user->number_of_reviews }}
                                {{ mb_ucfirst(trans_choice('user.reviews', $review->create_user->number_of_reviews)) }}
                            </div>
                        @endif

                        @if ($review->isCreatorIsSiteOwner())
                            <div class="mr-2 badge badge-pill badge-secondary">{{ __('Owner') }}</div>
                        @endif
                    </div>

                    <div class="ml-auto small text-right">
                        <meta itemprop="datePublished" content="{{ $review->created_at->format('Y-m-d') }}">
                        <x-time :time="$review->created_at"/>
                    </div>

                </div>

                @if ($showReviewedItem)
                    <div class="mb-1" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Organization">
                        {{ __('Review about') }}
                        <a href="{{ route('sites.show', $review->site) }}" class="font-weight-bold" itemprop="url">
                            <span itemprop="name">{{ $review->site->title }}</span>
                        </a>
                    </div>
                @endif

                <div class="mb-1" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                    <span class="text-secondary">{{ __('review.rate') }}</span>:
                    <meta itemprop="worstRating" content="1">
                    <x-site-rating :rating="$review->rate"/>
                    <span class="ml-1" itemprop="ratingValue">{{ $review->rate }}</span>/<span class="small" itemprop="bestRating">5</span>
                </div>

                @if (!empty($review->advantages))
                    <div class="mb-1">
                        <span class="text-success mr-1"><i class="fas fa-plus-circle"></i></span>
                        <span itemprop="description" style="white-space: pre-wrap;">{{ $review->advantages }}</span>
                    </div>
                @endif

                @if (!empty($review->disadvantages))
                    <div class="mb-1">
                        <span class="text-danger mr-1"><i class="fas fa-minus-circle"></i></span>
                        <span itemprop="description" style="white-space: pre-wrap;">{{ $review->disadvantages }}</span>
                    </div>
                @endif

                @if (!empty($review->comment))
                    <div class="mb-1 mr-1">
                        <span itemprop="description" style="white-space: pre-wrap;">{{ $review->comment }}</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-footer py-2 px-3 buttons">
            <div class="d-flex flex-row align-items-center">
                <a href="{{ route('reviews.rate.up', $review) }}"
                   class="rate_up btn btn-light btn-sm @if ($review->getAuthUserVote() > 0) active @endif">
                    <i class="far fa-thumbs-up"></i>
                </a>

                <div class="px-1 small rating">{{ $review->rating }}</div>

                <a href="{{ route('reviews.rate.down', $review) }}"
                   class="rate_down btn btn-light btn-sm @if ($review->getAuthUserVote() < 0) active @endif">
                    <i class="far fa-thumbs-down"></i>
                </a>

                @can('reply', $review)
                    <a href="{{ route('reviews.comments.create', $review) }}" class="reply btn btn-light btn-sm">
                        {{ __('Reply') }}
                    </a>
                @endcan

                <a href="{{ route('reviews.comments', $review) }}"
                   @if ($review->children_count < 1) style="display: none;" @endif
                   class="toggle_children btn btn-light btn-sm">
                    <span class="show_children">{{ __('Show replies') }}</span>
                    <span class="hide_children" style="display: none;">{{ __('Hide replies') }}</span>
                    <span class="count">{{ $review->children_count }}</span>
                </a>

                @can('edit', $review)
                    <a href="{{ route('reviews.edit', $review) }}" class="btn btn-light btn-sm">
                        {{ __('Edit') }}
                    </a>
                @endcan

                <a href="{{ route('reviews.destroy', $review) }}" class="delete btn btn-light btn-sm"
                   style="@cannot('delete', $review) display:none; @endcannot">
                    {{ __('Delete') }}
                </a>

                <a href="{{ route('reviews.destroy', $review) }}" class="restore btn btn-light btn-sm"
                   style="@cannot('restore', $review) display:none; @endcannot">
                    {{ __('Restore') }}
                </a>

            </div>
        </div>
    </div>

    <div class="descendants pl-3">
        @if (!empty($comments))
            @foreach ($comments as $comment)
                <x-comment :comment="$comment" :comments="$comments"/>
            @endforeach
        @endif
    </div>
</div>

