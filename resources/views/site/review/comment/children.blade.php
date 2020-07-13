@extends('layouts.app')

@section('content')

    @foreach ($comments as $comment)
        <x-comment :comment="$comment"/>
    @endforeach

@endsection
