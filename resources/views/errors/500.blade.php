@extends('layouts.app')

@section('content')

    <div class="card">
        <div class="card-body">
            500

            @isset ($exception)
                <p>{{ $exception->getMessage() }}</p>
            @endisset
        </div>
    </div>

@endsection
