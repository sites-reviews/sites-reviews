@extends('layouts.app')

@push('after_scripts')
    <script src="{{ mix('js/home.js', 'assets') }}"></script>
@endpush

@section('content')

    <h6 class="mb-3 ml-1">{{ __('Latest reviews about the sites') }}</h6>

    @if ($reviews->count() > 0)

        @foreach ($reviews as $review)
            <x-review :review="$review" showReviewedItem="true" />
        @endforeach

        @if ($reviews->hasPages())
            {{ $reviews->links() }}
        @endif

    @else
        <div class="alert alert-info">
            {{ __('No reviews have been left yet') }}
        </div>
    @endif

@endsection
