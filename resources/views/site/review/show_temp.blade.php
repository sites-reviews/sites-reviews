@extends('layouts.app')

@section('content')

    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading mb-3">
            {{ __('Your review is almost published') }}
        </h4>
        <p>{{ __('An email was sent to your email address :email to confirm the publication of the review', ['email' => $review->email]) }}.</p>
        <hr>
        <p class="mb-0">{{ __('Please open the email and click "Publish review"') }}</p>
    </div>

@endsection


