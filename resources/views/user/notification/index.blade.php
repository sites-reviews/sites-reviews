@extends('layouts.app')

@section('content')

    @if ($notifications->count() > 0)

        <div class="card">

            <div class="list-group list-group-flush">
                @foreach ($notifications as $notification)
                    @include('user.notification.item', ['notification' => $notification])
                @endforeach
            </div>

        </div>

        @if ($notifications->hasPages())
            {{ $notifications->links() }}
        @endif

    @else
        <div class="alert alert-info">
            {{ __('There are no new notifications yet') }}
        </div>
    @endif

@endsection
