@extends('layouts.app')

@section('content')

    @if (session('notification_send'))
        <div class="alert alert-success">
            <p>{{ __('Instructions for password recovery have been sent to the specified mailbox') }}</p>
        </div>
    @endif

    <div class="card">
        <div class="card-header">{{ __('Reset Password') }}</div>

        <div class="card-body">

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                               value="{{ old('email') ?? $email ?? '' }}" required autocomplete="email" autofocus>

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Send Password Reset Link') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
