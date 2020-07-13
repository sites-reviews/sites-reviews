console.log('app');

require ('jquery-form');
require ('jquery.scrollto');

let bootbox = require('bootbox');

bootbox.setDefaults({
    animate: false,
    backdrop: true,
    onEscape: true
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ajaxError(function (event, jqxhr, settings, thrownError) {

    console.log(thrownError);
    console.log(jqxhr);

    if (jqxhr.status == "401") {
        bootbox.alert('<div class="text-center">' + jqxhr.responseJSON.error + '</div>');
    }

    if (jqxhr.status == "403") {
        bootbox.alert('<div class="text-center">' + jqxhr.responseJSON.error + '</div>');
    }

    if (jqxhr.status == "419") {
        bootbox.alert('<div class="text-center">csrf error</div>');
    }
});

import NotificationDropdown from './components/notification-dropdown';

let $class = new NotificationDropdown;
$class.dropdown = $('#notificationsDropdown').first();
$class.init();

import HeaderSearch from './components/header-search';

let $headerSearch = new HeaderSearch;
$headerSearch.$header = $('header').first();
$headerSearch.init();

