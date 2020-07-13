@extends('layouts.app')

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('users.invitation.store.user', ['token' => $invitation->token]) }}" enctype="multipart/form-data" method="post">

                @csrf

                <div class="form-group row">
                    <label for="name" class="col-sm-2">{{ __('user.name') }}</label>
                    <div class="col-sm-10">
                        <input name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                               id="name" aria-describedby="nameHelp" value="{{ old('name') }}"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="gender" class="col-sm-2">{{ __('user.gender') }}</label>

                    <div class="col-sm-10">
                        @foreach (['male', 'female'] as $key)
                            <div class="form-check">
                                <input class="form-check-input{{ $errors->has('gender') ? ' is-invalid' : '' }}" type="radio"
                                       @if ($key == old('gender')) checked @endif
                                       name="gender" id="gender_{{ $key }}" value="{{ $key }}">
                                <label class="form-check-label" for="gender_{{ $key }}">
                                    {{ __('gender.'.$key) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-sm-2">{{ __('user.password') }}</label>
                    <div class="col-sm-10">
                        <input name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                               id="password" aria-describedby="passwordHelp" type="password" value="{{ old('password') }}"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password_confirmation" class="col-sm-2">{{ __('user.password_confirmation') }}</label>
                    <div class="col-sm-10">
                        <input name="password_confirmation" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                               id="password_confirmation"
                               aria-describedby="password_confirmationHelp" type="password" value="{{ old('password_confirmation') }}"/>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ __('Сontinue registration') }}
                </button>
            </form>

        </div>
    </div>

@endsection
