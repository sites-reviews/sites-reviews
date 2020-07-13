@extends('layouts.app')

@section('content')

    <div class="card d-flex flex-row px-3 py-2 mb-2">
        <div class="mr-3 text-center flex-shrink-1" style="width:50px;">
            <x-user-avatar :user="$comment->create_user" width="50" height="50" quality="90"/>
        </div>
        <div class="w-100">
            <div>
                <x-user-name :user="$comment->create_user"/>

                {{ mb_ucfirst(trans_choice('user.reviews', $comment->create_user->number_of_reviews)) }}:
                {{ $comment->create_user->number_of_reviews }}
            </div>
            <div class="mb-1">

                <form class="mt-2 comments-edit" action="{{ route('comments.update', ['comment' => $comment]) }}"
                      method="post" enctype="multipart/form-data">

                    @csrf
                    @method('patch')

                    <div class="form-group{{ $errors->update_comment->has('text') ? ' has-error' : '' }}">
                        <textarea name="text" class="form-control" id="text" aria-describedby="textHelp"
                                  placeholder="{{ __('comment.text_placeholder') }}">{{ $comment->text }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('common.save') }}
                    </button>
                </form>

            </div>
        </div>
    </div>

@endsection
