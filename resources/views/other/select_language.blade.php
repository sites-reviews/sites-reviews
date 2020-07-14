<ul class="list-group">
    @foreach ($locale as $lang => $flag)
        <a href="{{ route('locale.set', ['locale' => $lang]) }}"
           class="list-group-item list-group-item-action @if ($lang == App::getLocale()) active @endif">
            <span class="flag-icon flag-icon-{{ $flag }}"></span>
            {{ __('app.on_english', [], $lang) }} - {{ __('app.on_origin', [], $lang) }}
        </a>
    @endforeach
</ul>
