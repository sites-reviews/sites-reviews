<div class="card d-flex flex-row px-3 py-2 mb-2">
    <div class="mr-3 text-center flex-shrink-1" style="width:50px;">
        <x-user-avatar :user="$create_user" width="50" height="50" quality="90"/>
    </div>
    <div class="w-100">
        <div class="d-flex flex-row align-items-center">
            <div class="mr-2">
                <x-user-name :user="$create_user"/>
            </div>

            <div class="small text-lowercase">
                {{ $create_user->number_of_reviews }}
                {{ mb_ucfirst(trans_choice('user.reviews', $create_user->number_of_reviews)) }}
            </div>

        </div>
        <div class="mb-1">

            <form class="review-create mt-2" action="{{ route('reviews.store', ['site' => $site]) }}" method="post" enctype="multipart/form-data">

                @csrf

                <div class="form-group{{ $errors->store_review->has('rate') ? ' has-error' : '' }}">
                    <label for="rate">{{ __('review.rate') }}</label>

                    <div class="d-flex flex-row align-items-center">
                        <div class="h3 mb-0">
                            <x-site-rating :rating="0"/>
                        </div>

                        <div class="ml-3 name_container">

                        </div>
                    </div>

                    <input type="hidden" name="rate">
                </div>

                <div class="form-group{{ $errors->store_review->has('advantages') ? ' has-error' : '' }}">
                    <label for="advantages" class="text-success">{{ __('review.advantages') }}</label>
                    <textarea name="advantages" class="form-control" id="advantages" aria-describedby="advantagesHelp"
                              placeholder="{{ __('review.advantages_placeholder') }}"></textarea>
                </div>
                <div class="form-group{{ $errors->store_review->has('disadvantages') ? ' has-error' : '' }}">
                    <label for="disadvantages" class="text-danger">{{ __('review.disadvantages') }}</label>
                    <textarea name="disadvantages" class="form-control" id="disadvantages" aria-describedby="disadvantagesHelp"
                              placeholder="{{ __('review.disadvantages_placeholder') }}"></textarea>
                </div>
                <div class="form-group{{ $errors->store_review->has('comment') ? ' has-error' : '' }}">
                    <label for="comment" class="text-secondary">{{ __('review.comment') }}</label>
                    <textarea name="comment" class="form-control" id="comment" aria-describedby="commentHelp"
                              placeholder="{{ __('review.comment_placeholder') }}"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Publish') }}</button>
            </form>

        </div>
    </div>
</div>
