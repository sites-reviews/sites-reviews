@extends('layouts.app')

@section('content')

    <div class="card">
        <div class="card-body">
            403 {{ $exception->getMessage() }}
        </div>
    </div>

@endsection
