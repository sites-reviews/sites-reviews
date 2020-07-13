@extends('layouts.app')

@section('content')

    <div class="card mb-2">
        <div class="card-body text-center">

            <div class="">
                <img class="img-fluid" itemprop="image" alt="{{ $user->userName }}"
                     src="{{ $user->avatar->fullUrlMaxSize($width, $height, $quality) }}"/>
            </div>
        </div>
    </div>

@endsection
