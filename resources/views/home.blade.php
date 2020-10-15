@extends('layouts.app')

@push('after_scripts')
    <script src="{{ mix('js/home.js', 'assets') }}"></script>
@endpush

@section('content')

    <section class="jumbotron text-center">
        <div class="container text-center">

            <h1 class="jumbotron-heading">
                {{ __("Everyone's opinion matters") }}
            </h1>

            <p class="lead text-muted">
                {{ __('Read and write reviews about sites and companies') }}
            </p>

            <form action="{{ route('sites.search') }}" enctype="multipart/form-data">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-8 col-sm-8 col-12">

                            <input name="term" class="form-control form-control-lg" type="text"
                                   placeholder="{{ __('Enter the name of site or company') }}">

                        </div>
                        <div class="mt-3 mt-sm-0">
                            <button type="submit" class="btn btn-primary mb-2 btn-lg">{{ __('Search') }}</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>

    <div class="text-center">
        <h5 class="mb-4 ml-1">{{ __('Latest reviews') }}</h5>
    </div>

    @if ($reviews->count() > 0)

        @foreach ($reviews as $review)
            <x-review :review="$review" showReviewedItem="true"/>
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
