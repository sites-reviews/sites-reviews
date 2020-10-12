<div class="mb-3 align-items-center justify-content-md-center d-flex flex-column flex-md-row">
    <a class="btn btn-lg btn-light align-items-center d-flex" style="color:#4267B2"
       href="{{ route('social_accounts.redirect', ['provider' => 'facebook']) }}">
        <i class="fab fa-facebook-square h4 mb-0 mr-2"></i> {{ __('Log in via') }} Facebook</a>

    <a class="btn btn-lg btn-light align-items-center d-flex" style="color:#D64937"
       href="{{ route('social_accounts.redirect', ['provider' => 'google']) }}">
        <i class="fab fa-google h4 mb-0 mr-2"></i> {{ __('Log in via') }} Google</a>

    <a class="btn btn-lg btn-light align-items-center d-flex" style="color:#4A76A8"
       href="{{ route('social_accounts.redirect', ['provider' => 'vkontakte']) }}">
        <i class="fab fa-vk h4 mb-0 mr-2"></i> {{ __('Log in via') }} Vk</a>
</div>
