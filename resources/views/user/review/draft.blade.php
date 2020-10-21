@extends('layouts.app')

@push('after_scripts')
    <script src="{{ mix('js/users.reviews.draft.js', 'assets') }}" defer></script>
@endpush

@section('content')

    <div id="reviews" class="reviews">

        <h5>
            {{ __('Drafts') }} <span
                class="badge badge-info">{{ $user->number_of_draft_reviews }}</span>
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
