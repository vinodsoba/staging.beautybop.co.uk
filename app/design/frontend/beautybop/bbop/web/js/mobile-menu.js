define(['jquery'], function ($) {
    'use strict';

    return function () {

        $('#mobile-menu-toggle').on('click', function () {
            $('#mobile-menu').toggleClass('hidden');
        });

    };
});