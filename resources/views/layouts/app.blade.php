<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (empty($title = SEOMeta::getTitle()))
        <title>{{ \DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::pageTitle() }}</title>
    @endif

    {!! SEOMeta::generate() !!}
    {!! \Artesaos\SEOTools\Facades\OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    {!! JsonLd::generate() !!}

<!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css"
          integrity="sha256-46r060N2LrChLLb5zowXQ72/iKKNiw/lAmygmHExk/o=" crossorigin="anonymous"/>

    <!-- Styles -->
    <link href="{{ mix('css/app.css', 'assets') }}" rel="stylesheet">
    <link href="{{ mix('css/bootstrap.css', 'assets') }}" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    @if (!empty(\Illuminate\Support\Facades\Route::current()))
        @foreach (config('app.locales') as $locale)
            @if ($locale != \Illuminate\Support\Facades\App::getLocale())
                <link rel="alternate" hreflang="{{ $locale }}"
                      href="{{ route(\Route::currentRouteName(), array_merge(\Illuminate\Support\Facades\Route::current()->parameters(), \Illuminate\Support\Facades\Request::all(), ['locale' => $locale])) }}"/>
            @endif
        @endforeach
    @endif
    {{--
    <script data-ad-client="ca-pub-8879521378582869" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
--}}
</head>
<body itemscope itemtype="http://schema.org/WebPage">
<div id="app">
    <header class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
        <div class="container d-flex flex-row flex-nowrap">

            <div class="left-side navbar-nav flex-row align-items-center">

                <a id="logo" class="navbar-brand mr-2" href="{{ route('home') }}">

                    <div class="logo_svg d-inline-block align-top"
                         style="width:4rem; height:2rem; background-size:contain;"></div>

                    <h1 class="d-none d-md-inline h5">{{ config('app.name') }}</h1>
                </a>

                @empty($dont_show_header_search)

                    <button id="close_header_search" class="btn btn-secondary mr-3" style="display:none;">
                        <i class="fas fa-times"></i>
                    </button>

                    <form id="header-search" class="form-inline my-lg-0 d-flex flex-nowrap mr-2" action="{{ route('sites.search') }}"
                          enctype="multipart/form-data">

                        <input name="term" class="form-control mr-2 d-none d-sm-block" type="search"
                               @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'sites.search') value="{{ request()->term }}" @endif
                               placeholder="{{ __('Search') }}" aria-label="Search" required>

                        <button class="btn btn-outline-success my-2 my-sm-0 text-nowrap" type="submit">
                            <i class="fas fa-search"></i>
                        </button>

                    </form>

                @endempty

            </div>

            <div class="right-side navbar-nav flex-row align-items-center">

                @auth
                    <div id="notificationsDropdown" class="nav-item d-flex align-items-center dropdown mr-2">

                        <button id="notificationsDropdownButton" data-href="{{ route('users.notifications.dropdown', Auth::user()) }}"
                                class="btn btn-secondary text-nowrap" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" v-pre>
                            <i class="far fa-bell"></i>
                            <span class="badge badge-danger">{{ ($count = Auth::user()->unreadNotifications->count()) ? $count : '' }}</span>
                        </button>

                        <div class="dropdown-menu position-absolute dropdown-menu-right"
                             aria-labelledby="notificationsDropdownButton">

                            <div class="dropdown-header text-center">{{ __('Your notifications') }}</div>

                            <div class="content" style="min-height: 300px">
                                <div class="text-center ">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only">{{ __('Loading...') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-footer text-center">
                                <a href="{{ route('users.notifications', ['user' => \Illuminate\Support\Facades\Auth::user()]) }}">{{ __('All notifications') }}</a>
                            </div>
                        </div>
                    </div>

                @endauth

                @guest

                    <div class="login nav-item">
                        <a class="nav-link px-2" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </div>

                    @if (Route::has('users.invitation.create'))
                        <div class="registation nav-item">
                            <a class="btn btn-primary" href="{{ route('users.invitation.create') }}">{{ __('Registration') }}</a>
                        </div>
                    @endif

                @else

                    <div class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex flex-row align-items-center text-light px-0" href="#"
                           role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false" v-pre>
                            <x-user-avatar :user="Auth::user()" width="35" height="35" quality="90" url="0"/>

                            <span class="ml-2 d-none d-md-inline">{{ \Illuminate\Support\Facades\Auth::user()->name }}</span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right position-absolute" aria-labelledby="navbarDropdown">

                            <a class="dropdown-item" href="{{ route('users.show', ['user' => Auth::user(), '#reviews']) }}">
                                {{ __('user.my_reviews') }}
                                <span class="badge badge-info">{{ Auth::user()->number_of_reviews }}</span>
                            </a>


                            @if (Auth::user()->number_of_draft_reviews > 0)

                                <a class="dropdown-item" href="{{ route('users.reviews.draft', ['user' => Auth::user()]) }}">
                                    {{ __('Drafts') }}
                                    <span class="badge badge-info">{{ Auth::user()->number_of_draft_reviews }}</span>
                                </a>

                            @endif

                            <a class="dropdown-item" href="{{ route('users.settings', Auth::user()) }}">
                                <i class="fas fa-cog"></i> {{ __('user.settings') }}
                            </a>

                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <div class="jumbotron py-3 px-0">

        <main style="min-height:500px;">

            <div class="container">
                {{ \DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::render() }}
            </div>

            <div class="container">

                @if ($errors->any())
                    @if ($errors->count() == 1)
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>

        </main>

        <footer class="mt-3 mb-5" style="height:50px;">
            <div class="container ">
                @if (\Browser::isFirefox())
                    <div class="mb-3 text-center">
                        <a href="https://addons.mozilla.org/firefox/addon/sites-reviews-com/" class="h4 d-block" rel="nofollow" target="_blank">
                            <i class="fab fa-firefox-browser"></i> {{ __('Install extension for Firefox') }}
                        </a>
                    </div>
                @elseif (\Browser::isEdge())
                    <div class="mb-3 text-center">
                        <a href="https://microsoftedge.microsoft.com/addons/detail/sitesreviewscom-revie/lddfbdnnfdijagkaglcfhdoojpmonbne"
                           class="h4 d-block" rel="nofollow" target="_blank">
                            <i class="fab fa-edge"></i> {{ __('Install extension for Edge') }}
                        </a>
                    </div>
                @elseif (\Browser::isChrome())
                    <div class="mb-3 text-center">
                        <a href="https://chrome.google.com/webstore/detail/sites-reviewscom-reviews/ellmdjpbdlololefgdbbmjlgjmlopkoa"
                           class="h4 d-block" rel="nofollow" target="_blank">
                            <i class="fab fa-chrome"></i> {{ __('Install extension for Chrome') }}
                        </a>
                    </div>
                @endif

                <div class=" d-flex flex-sm-row flex-column align-items-center">

                    @if (!empty(\Illuminate\Support\Facades\Route::current()))

                        <button type="button" class="btn btn-light mr-3" data-toggle="modal" data-target="#selectLanguageModal">
                            {{ __('Language') }}:

                            <span class="flag-icon flag-icon-{{ config('app.local_flag_map.'.App::getLocale()) }}"></span>

                            {{ __('app.on_english') }} - {{ __('app.on_origin') }}
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="selectLanguageModal" tabindex="-1" role="dialog" aria-labelledby="selectLanguageModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="selectLanguageModalLabel">{{ __('Select language') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <ul class="list-group">
                                            @foreach (config('app.local_flag_map') as $lang => $flag)
                                                <a href="{{ currentRouteUrlWithParameters(['locale' => $lang]) }}"
                                                   class="list-group-item list-group-item-action @if ($lang == App::getLocale()) active @endif">
                                                    <span class="flag-icon flag-icon-{{ $flag }}"></span>
                                                    {{ __('app.on_english', [], $lang) }} - {{ __('app.on_origin', [], $lang) }}
                                                </a>
                                            @endforeach
                                        </ul>

                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif

                    <div class="mt-3 mt-sm-0">{{ __('Contacts') }}: sites.reviews.com@gmail.com</div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('privacy.policy') }}" class="small mr-3">{{ __('Privacy policy') }}</a>

                    <a href="{{ route('personal_data_processing_agreement') }}" class="small">{{ __('Personal data processing agreement') }}</a>
                </div>
            </div>
        </footer>

    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
        crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/js/bootstrap.bundle.min.js"
        integrity="sha512-Oy5BruJdE3gP9+LMJ11kC5nErkh3p4Y0GawT1Jrcez4RTDxODf3M/KP3pEsgeOYxWejqy2SPnj+QMpgtvhDciQ=="
        crossorigin="anonymous"></script>

<script src="{{ mix('js/app.js', 'assets') }}"></script>

@stack('after_scripts')

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (m, e, t, r, i, k, a) {
        m[i] = m[i] || function () {
            (m[i].a = m[i].a || []).push(arguments)
        };
        m[i].l = 1 * new Date();
        k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
    })
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(68301934, "init", {
        clickmap: true,
        trackLinks: true,
        accurateTrackBounce: true,
        webvisor: true
    });
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/68301934" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->

</body>
</html>
