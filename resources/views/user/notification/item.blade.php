<a class="list-group-item list-group-item-action @if ($notification->read_at) text-black-50 @else text-body @endif"
   href="{{ $notification->data['url'] }}">
    <div class="d-flex w-100 justify-content-between">
        <h6 class="mb-1">{{ $notification->data['title'] }}</h6>
        <small class="ml-2 text-muted text-nowrap">{{ $notification->created_at->diffForHumans() }}</small>
    </div>
    <p class="mb-0">{{ $notification->data['description'] }}</p>
    @if (empty($notification->read_at))
        <p class="mb-0">{{ __('New') }}</p>
    @endif
</a>
