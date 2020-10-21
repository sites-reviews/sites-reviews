@extends('layouts.app')

@push('after_scripts')

    <script src="{{ mix('js/reviews.create.js', 'assets') }}" defer></script>

@endpush

@section('content')

    <div class="card mb-3">
        <div class="card-body d-flex flex-column flex-sm-row ">

            <div class="flex-shrink-1 text-center mr-3 mb-md-0 mb-3" style="width:100px;">
                <x-site-preview :site="$site" width="100" height="100" url="0"/>
            </div>

            <div class="w-100">
                <a href="{{ $site->getUrl() }}" target="_blank">
                    <h5 itemprop="name">{{ $site->title }}</h5>
                </a>
                <h6 itemprop="description">{{ \Illuminate\Support\Str::limit($site->description, 50) }}</h6>
            </div>
        </div>
    </div>

    @if ($errors->store_review->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->store_review->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h5>{{ __('Your review') }}:</h5>

    @include('site.review.create_form', ['create_user' => null])

@endsection
