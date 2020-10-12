@extends('user.setting.layout')

@section('user_setting_content')

    <div class="card">
        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success alert-dismissable">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                </div>
            @endif

            <div class="list-group list-group-flush">

                @foreach ($providers as $provider)
                    <div class="list-group-item d-flex ">
                        <div class="w-100">{{ __(mb_ucfirst($provider)) }}</div>

                        <div class="flex-shrink-1 d-flex flex-row align-items-center">

                            @if (empty($account = $social_accounts->where('provider', $provider)->first()))
                                <a class="btn btn-light"
                                   href="{{ route('social_accounts.redirect', ['provider' => $provider]) }}">
                                    {{ __('Bind') }}
                                </a>
                            @else
                                <div class="mx-3">
                                    {{ __('Binded') }}
                                </div>

                                <div class="btn-group">
                                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item"
                                           href="{{ route('users.social_accounts.unbind', ['user' => $user->id, 'id' => $account->id]) }}">
                                            {{ __('Unbind') }}
                                        </a>
                                    </div>
                                </div>

                            @endif
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

@endsection
