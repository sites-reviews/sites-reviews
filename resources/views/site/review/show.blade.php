@extends('layouts.app')

@push('after_scripts')
    <script src="{{ mix('js/reviews.show.js', 'assets') }}" defer></script>
@endpush

@section('content')

    <x-review :review="$review" :comments="$comments" />

@endsection
