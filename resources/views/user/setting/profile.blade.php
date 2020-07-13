@extends('user.setting.layout')

@section('user_setting_content')

    <div class="card mb-3">
        <div class="card-body d-flex">

            <div class="flex-shrink-1 d-flex justify-content-center mr-3" style="width:220px;">
                <x-user-avatar :user="$user" width="100" height="100" quality="90"/>
            </div>

            <div class="w-100">

                <form action="{{ route('users.avatar.store', $user) }}" method="post" enctype="multipart/form-data">

                    @csrf

                    <div class="form-group">
                        <label for="exampleFormControlFile1">{{ __('user.avatar') }}</label>
                        <input name="avatar" type="file" class="form-control-file" id="exampleFormControlFile1">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('common.upload') }}
                    </button>
                </form>

            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('users.update', $user) }}" method="post" enctype="multipart/form-data">

                @csrf
                @method('patch')

                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name">{{ __('user.name') }}</label>
                    <input name="name" class="form-control" id="name" aria-describedby="nameHelp" value="{{ $user->name }}" />
                </div>

                <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
                    <label for="gender" >{{ __('user.gender') }}</label>

                    @foreach (['male', 'female'] as $key)
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                   @if ($key == $user->gender) checked @endif
                                   name="gender" id="gender_{{ $key }}" value="{{ $key }}">
                            <label class="form-check-label" for="gender_{{ $key }}">
                                {{ __('gender.'.$key) }}
                            </label>
                        </div>
                    @endforeach

                </div>

                <button type="submit" class="btn btn-primary">
                    {{ __('common.save') }}
                </button>
            </form>

        </div>
    </div>

@endsection
