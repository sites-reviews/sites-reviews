@extends('user.setting.layout')

@section('user_setting_content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('users.settings.notifications.update', $user) }}" method="post" enctype="multipart/form-data">

                @csrf

                <h5 class="card-title">{{ __('Notifications to your email address') }}: {{ $user->email }}</h5>

                @foreach ($user->notificationSetting->getEmailNotifications() as $name)

                    <div class="form-group form-check{{ $errors->has($name) ? ' has-error' : '' }}">
                        <input name="{{ $name }}" type="hidden" value="0">
                        <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="1"
                            @if ($user->notificationSetting->{$name}) checked @endif
                               class="form-check-input">
                        <label class="form-check-label" for="{{ $name }}">{{ __('user_notification_setting.'.$name) }}</label>
                    </div>

                @endforeach

                <h5 class="card-title">{{ __('Notice on the website') }}</h5>

                @foreach ($user->notificationSetting->getDBNotifications() as $name)

                    <div class="form-group form-check{{ $errors->has($name) ? ' has-error' : '' }}">
                        <input name="{{ $name }}" type="hidden" value="0">
                        <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="1"
                               @if ($user->notificationSetting->{$name}) checked @endif
                               class="form-check-input">
                        <label class="form-check-label" for="{{ $name }}">{{ __('user_notification_setting.'.$name) }}</label>
                    </div>

                @endforeach

                <button type="submit" class="btn btn-primary">
                    {{ __('Save') }}
                </button>
            </form>

        </div>
    </div>

@endsection
