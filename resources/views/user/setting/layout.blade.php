@extends('layouts.app')

@section('content')

    <ul class="nav mb-3 nav-pills">
        <li class="nav-item">
            <a class="nav-link {{ active('users.settings') }}"
               href="{{ route('users.settings', $user) }}">
                {{ __('Profile') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ active('users.settings.notifications') }}"
               href="{{ route('users.settings.notifications', $user) }}">
                {{ __('Notifications') }}
            </a>
        </li>
    </ul>

    @yield('user_setting_content')

@endsection
