@extends('layouts.app')

@section('content')

    @if (session('invitation_was_sent'))
        <div class="alert alert-success">
            <h4 class="alert-heading">{{ __('The email was successfully sent to your mailbox :email', ['email' => session('email')]) }}</h4>
            <p>{{ __('Now open your mailbox :email and click on the button in the email', ['email' => session('email')]) }}</p>
            <hr>
            <p>{{ __("If you haven't received an email in 5 minutes:") }}</p>
            <ul class="mb-0">
                <li>{{ __("Check the mailbox address you entered") }}</li>
                <li>{{ __('Check the spam folder') }}</li>
                <li>{{ __('Try entering a different mailbox') }}</li>
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <div class="mb-2">
                {{ __('Please enter your email address where the account will be registered:') }}
            </div>

            <form action="{{ route('users.invitation.create') }}" enctype="multipart/form-data" method="post" class="mb-3">

                @csrf

                <div class="form-group">
                    <input name="email" type="email"
                           class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                           id="exampleInputEmail1" aria-describedby="emailHelp"
                           placeholder="{{ __('Enter your mailbox') }}">
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Сontinue registration') }}</button>
            </form>

            <div>
                {{ __("If you haven't received an email in 5 minutes:") }} <br/>
                <ul class="mb-0">
                    <li>{{ __("Check the mailbox address you entered") }}</li>
                    <li>{{ __('Check the spam folder') }}</li>
                    <li>{{ __('Try entering a different mailbox') }}</li>
                </ul>
            </div>

        </div>
    </div>

@endsection
