<div class="card d-flex flex-row px-3 py-2 mb-2">
    <div class="mr-3 text-center flex-shrink-1" style="width:50px;">
        <x-user-avatar :user="$create_user" width="50" height="50" quality="90"/>
    </div>
    <div class="w-100">
        <div class="d-flex flex-row align-items-center">
            @isset($create_user)
                <div class="mr-2">
                    <x-user-name :user="$create_user"/>
                </div>

                <div class="small text-lowercase">
                    {{ $create_user->number_of_reviews }}
                    {{ mb_ucfirst(trans_choice('user.reviews', $create_user->number_of_reviews)) }}
                </div>
            @endisset
        </div>
        <div class="mb-1">

            <form class="review-create mt-2" action="{{ route('reviews.store', ['site' => $site]) }}" method="post"
                  enctype="multipart/form-data">

                @csrf

                <div class="form-group{{ $errors->store_review->has('rate') ? ' is-invalid' : '' }}">
                    <label for="rate">{{ __('review.rate') }}</label>

                    <div class="d-flex flex-row align-items-center">
                        <div class="h3 mb-0">
                            <x-site-rating :rating="old('rate') ?? 0"/>
                        </div>

                        <div class="ml-3 name_container">

                        </div>
                    </div>

                    <input type="hidden" name="rate"/>
                </div>

                <div class="form-group" @if (!$errors->store_review->any()) style="display: none;" @endif>
                    <label for="advantages" class="text-success">{{ __('review.advantages') }}</label>
                    <textarea id="advantages" name="advantages" class="form-control {{ $errors->store_review->has('advantages') ? ' is-invalid' : '' }}"
                              id="advantages" aria-describedby="advantagesHelp"
                              placeholder="{{ __('review.advantages_placeholder') }}">{{ old('advantages') }}</textarea>
                </div>
                <div class="form-group" @if (!$errors->store_review->any()) style="display: none;" @endif>
                    <label for="disadvantages" class="text-danger">{{ __('review.disadvantages') }}</label>
                    <textarea id="disadvantages" name="disadvantages" class="form-control {{ $errors->store_review->has('disadvantages') ? ' is-invalid' : '' }}"
                              id="disadvantages" aria-describedby="disadvantagesHelp"
                              placeholder="{{ __('review.disadvantages_placeholder') }}">{{ old('disadvantages') }}</textarea>
                </div>
                <div class="form-group" @if (!$errors->store_review->any()) style="display: none;" @endif>
                    <label for="comment" class="text-secondary">{{ __('review.comment') }}</label>
                    <textarea id="comment" name="comment" class="form-control {{ $errors->store_review->has('comment') ? ' is-invalid' : '' }}"
                              id="comment" aria-describedby="commentHelp"
                              placeholder="{{ __('review.comment_placeholder') }}">{{ old('comment') }}</textarea>
                </div>

                @if (!auth()->check())
                    <div class="form-group" @if (!$errors->store_review->any()) style="display: none;" @endif>
                        <label for="email" class="text-secondary">{{ __('review.email') }}</label>
                        <input id="email" type="text" name="email"
                               class="form-control {{ $errors->store_review->has('email') ? ' is-invalid' : '' }}"
                               placeholder="{{ __('Enter your mailbox address in this field') }}"
                               aria-describedby="emailHelp"/>
                    </div>
                @endif

                <div id="btn-publish" class="form-group" @if (!$errors->store_review->any()) style="display: none;" @endif>

                    <button type="submit" class="btn btn-primary">{{ __('Publish') }}</button>

                </div>

            </form>

        </div>
    </div>
</div>
