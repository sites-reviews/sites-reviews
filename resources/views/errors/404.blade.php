@extends('layouts.app')

@section('content')

    <div class="alert alert-warning">
        <h5>{{ __('Error') }} 404</h5>

        @isset ($exception)
            <p>{{ $exception->getMessage() }}</p>
        @endisset

    </div>

@endsection
