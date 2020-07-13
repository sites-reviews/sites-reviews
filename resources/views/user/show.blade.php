@extends('layouts.app')

@push('after_scripts')
    <script src="{{ mix('js/users.show.js', 'assets') }}" defer></script>
@endpush

@section('content')

    <div class="card mb-2" itemprop="mainEntity" itemscope itemtype="http://schema.org/Person">
        <div class="card-body d-flex">

            <div class="flex-shrink-1 d-flex justify-content-center mr-3" style="width:220px;">
                <x-user-avatar :user="$user" width="100" height="100" quality="90"/>
            </div>

            <div class="w-100">
                <h2 itemprop="name">{{ $user->name }}</h2>

                <div>
                    {{ mb_ucfirst(trans_choice('user.reviews', $user->number_of_reviews)) }}:
                    {{ $user->number_of_reviews }}
                </div>

                <div>
                    {{ __('user.rating') }}:
                    {{ $user->rating }}
                </div>

            </div>

        </div>
    </div>

    <div id="reviews" class="reviews">

        <h5>
            {{ mb_ucfirst(trans_choice('user.reviews', $user->number_of_reviews)) }} <span
                class="badge badge-info">{{ $user->number_of_reviews }}</span>
        </h5>

        @if ($reviews->count() > 0)

            @foreach ($reviews as $review)
                <x-review :review="$review" showReviewedItem="1" showUserReviewsCount="0"/>
            @endforeach

            {{ $reviews->links() }}

        @else
            <div class="alert alert-info">
                {{ __('user.no_reviews_have_been_left_yet') }}
            </div>
        @endif

    </div>

@endsection
