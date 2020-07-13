@if ($unreadNotifications->count() > 0)

    <div class="list-group list-group-flush">
        @foreach ($unreadNotifications as $notification)
            @include('user.notification.item', ['notification' => $notification])
        @endforeach
    </div>
@else
    <div >{{ __('There are no new notifications yet') }}</div>
@endif
