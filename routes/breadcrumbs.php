<?php

Breadcrumbs::for('home', function ($trail) {
    //$trail->push(__('Home page'), route('home'));
});

Breadcrumbs::for('sites.show', function ($trail, $site) {
    $trail->parent('home');

    if ($site instanceof \App\Site)
        $trail->push(mb_substr($site->title, 0, 50), route('sites.show', $site->domain));
});

Breadcrumbs::for('sites.edit', function ($trail, $site) {
    $trail->parent('sites.show', $site);
    $trail->push(__('Edit'), route('sites.edit', $site));
});
/*
Breadcrumbs::for('users.show', function ($trail, $name) {
    $trail->parent('home');
    $trail->push(mb_substr($name->name, 0, 50), route('users.show', $name));
});*/

Breadcrumbs::for('sites.verification.request', function ($trail, $site) {
    $trail->parent('sites.show', $site);
    $trail->push(__('Verification'), route('sites.verification.request', $site));
});

Breadcrumbs::for('sites.search', function ($trail) {
    $term = request()->term;

    $trail->parent('home');
    $trail->push(mb_substr($term, 0, 50), route('sites.search', ['term' => $term]));
});

Breadcrumbs::for('reviews.edit', function ($trail, $review) {
    $trail->parent('home');
    $trail->push(__('Edit review'), route('reviews.edit', ['review' => $review]));
});

Breadcrumbs::for('login', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Login'), route('login'));
});

Breadcrumbs::for('users.invitation.create', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Registration'), route('users.invitation.create'));
});

Breadcrumbs::for('users.invitation.create.user', function ($trail, $token) {
    $trail->parent('home');
    $trail->push(__('Registration'), route('users.invitation.create.user', ['token' => $token]));
});

Breadcrumbs::for('users.settings', function ($trail, $user) {
    $trail->parent('users.show', $user);
    $trail->push(__('Profile settings'), route('users.settings', ['user' => $user]));
});

Breadcrumbs::for('users.settings.notifications', function ($trail, $user) {
    $trail->parent('users.show', $user);
    $trail->push(__('Notification settings'), route('users.settings.notifications', ['user' => $user]));
});

Breadcrumbs::for('password.request', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Reset Password'), route('password.request'));
});

Breadcrumbs::for('password.reset', function ($trail, $token) {
    $trail->parent('home');
    $trail->push(__('Reset Password'), route('password.reset', ['token' => $token]));
});

Breadcrumbs::for('users.notifications', function ($trail, $user) {
    $trail->parent('users.show', $user);
    $trail->push(__('Notifications'), route('users.show', ['user' => $user]));
});

Breadcrumbs::for('privacy.policy', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Privacy policy'), route('privacy.policy'));
});

Breadcrumbs::for('personal_data_processing_agreement', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Personal data processing agreement'), route('personal_data_processing_agreement'));
});

Breadcrumbs::macro('pageTitle', function () {
    $title = ($breadcrumb = Breadcrumbs::current()) ? "{$breadcrumb->title} – " : '';

    if (($page = (int) request('page')) > 1) {
        $title .= __('Page')." $page – ";
    }

    return $title . ' '. __('app.title');
});
