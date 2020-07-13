@extends('layouts.app')

@section('content')

    @if ($sites->count() > 0)

        @foreach ($sites as $site)
            @include('site.item')
        @endforeach

        @if ($sites->hasPages())
            {{ $sites->links() }}
        @endif

    @else

        <div class="alert alert-info">

        </div>
    @endif


@endsection
