@extends('layouts.app')

@section('content')

    <div class="card d-flex flex-row px-3 py-2 mb-2">
        <div class="mr-3 text-center flex-shrink-1" style="width:50px;">
            <x-user-avatar :user="auth()->user()" width="50" height="50" quality="90"/>
        </div>
        <div class="w-100">
            <div>
                <x-user-name :user="auth()->user()"/>

                {{ mb_ucfirst(trans_choice('user.reviews', auth()->user()->number_of_reviews)) }}:
                {{ auth()->user()->number_of_reviews }}
            </div>
            <div class="mb-1">

                <form class="review-comment-create mt-2" action="{{ route('reviews.comments.store', ['review' => $review]) }}" method="post"
                      enctype="multipart/form-data">

                    @csrf

                    <div class="form-group{{ $errors->has('text') ? ' has-error' : '' }}">
                        <textarea name="text" class="form-control" id="text" aria-describedby="textHelp"
                                  placeholder="{{ __('comment.text_placeholder') }}" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Publish') }}</button>
                </form>

            </div>
        </div>
    </div>

@endsection
