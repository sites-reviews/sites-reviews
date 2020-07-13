@extends('layouts.app')

@push('after_scripts')
    <script src="{{ mix('js/reviews.edit.js', 'assets') }}" defer></script>
@endpush

@section('content')

    <div class="card d-flex flex-row px-3 py-2 mb-2">
        <div class="mr-3 text-center flex-shrink-1" style="width:50px;">
            <x-user-avatar :user="$review->create_user" width="50" height="50" quality="90"/>
        </div>
        <div class="w-100">
            <div>
                <x-user-name :user="$review->create_user"/>

                {{ mb_ucfirst(trans_choice('user.reviews', $review->create_user->number_of_reviews)) }}:
                {{ $review->create_user->number_of_reviews }}
            </div>
            <div class="mb-1">

                <form class="mt-2 reviews-edit" action="{{ route('reviews.update', ['review' => $review]) }}"
                      method="post" enctype="multipart/form-data">

                    @csrf
                    @method('patch')

                    <div class="form-group{{ $errors->update_review->has('rate') ? ' has-error' : '' }}">
                        <label for="rate">{{ __('review.rate') }}</label>

                        <div class="d-flex flex-row align-items-center">
                            <div class="h3 mb-0">
                                <x-site-rating :rating="$review->rate" />
                            </div>

                            <div class="ml-3 name_container">

                            </div>
                        </div>

                        <input type="hidden" name="rate" value="{{ $review->rate }}">
                    </div>

                    <div class="form-group{{ $errors->update_review->has('advantages') ? ' has-error' : '' }}">
                        <label for="advantages" class="text-success">{{ __('review.advantages') }}</label>
                        <textarea name="advantages" class="form-control" id="advantages" aria-describedby="advantagesHelp"
                                  placeholder="{{ __('review.advantages_placeholder') }}">{{ $review->advantages }}</textarea>
                    </div>
                    <div class="form-group{{ $errors->update_review->has('disadvantages') ? ' has-error' : '' }}">
                        <label for="disadvantages" class="text-danger">{{ __('review.disadvantages') }}</label>
                        <textarea name="disadvantages" class="form-control" id="disadvantages" aria-describedby="disadvantagesHelp"
                                  placeholder="{{ __('review.disadvantages_placeholder') }}">{{ $review->disadvantages }}</textarea>
                    </div>
                    <div class="form-group{{ $errors->update_review->has('comment') ? ' has-error' : '' }}">
                        <label for="comment" class="text-secondary">{{ __('review.comment') }}</label>
                        <textarea name="comment" class="form-control" id="comment" aria-describedby="commentHelp"
                                  placeholder="{{ __('review.comment_placeholder') }}">{{ $review->comment }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('common.save') }}
                    </button>
                </form>

            </div>
        </div>
    </div>

@endsection
