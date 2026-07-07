define([
    'jquery'
], function ($) {
    'use strict';

    console.log('Announcement component loaded');

    $(function () {

        var messages = $('.bb-announcement span');

        if (!messages.length) {
            return;
        }

        var current = 0;

        messages.hide().eq(0).show();

        setInterval(function () {

            messages.eq(current).fadeOut(400);

            current = (current + 1) % messages.length;

            messages.eq(current).fadeIn(400);

        }, 5000);

    });
});