@extends('layouts.app')

@section('content')

    @if ($sites->count() > 0)

        @foreach ($sites as $site)
            @include('site.item')
        @endforeach

        @if ($sites->hasPages())
            {{ $sites->appends(['term' => $term])->links() }}
        @endif

    @else

        <div class="alert alert-info">
            {{ __("The search didn't yield any results") }}
        </div>
    @endif

    @if ($errors->create_site->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->create_site->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($addSite)
        <a class="btn btn-primary" href="{{ route('sites.create.or_show', ['domain' => $domain]) }}">
            {{ __("Add site :site ?", ['site' => $domain]) }}
        </a>
    @endif

@endsection
