@extends('layouts.app')

@push('after_scripts')
    <script src="{{ mix('js/comments.show.js', 'assets') }}" defer></script>
@endpush

@section('content')

    <x-comment :comment="$comment" :descendants="$descendants" />

@endsection
