define(['jquery', 'domReady!'], function ($) {
    'use strict';

    function responsive() {
        var width = $(window).width();

        if (width > 100 && width < 1023) {
            $('.block-search input').css({
                display: 'block'
            });
        }
    }

    // filter dropdown
    $(document).on('click', '.filter-options-title', function () {
        $(this).next('.filter-options-content').toggle();
    });

    // navigation hover
    $('li.level0.nav-2').hover(function () {
        $('ul.level1.submenu').addClass('nav-dropdown feature-navigation');
    });

    // mini search behaviour
    if ($(window).width() > 1024) {
        $('.field.search').hide();
        $('.row.section-item-content-bottom').hide();
    } else {
        $('.field.search').show();
    }

    // mobile sticky header
    if ($(window).width() < 1024) {
        var lastScrollTop = 0;
        var delta = 5;

        $(window).on('scroll', function () {
            var st = $(this).scrollTop();

            if (Math.abs(lastScrollTop - st) <= delta) {
                return;
            }

            if (st > lastScrollTop) {
                $('.page-header').css({
                    position: 'sticky',
                    top: 0,
                    zIndex: 111,
                    width: '100%'
                });
            } else {
                if (lastScrollTop === 0) {
                    $('.page-header').css({position: 'unset'});
                }
            }

            lastScrollTop = st;
        });
    }

    // mobile submenu
    if ($(window).width() < 1023) {
        $(document).on('click', '.level1 a', function () {
            $('.level1.submenu.submenu.feature-navigation').slideToggle();
        });
    }

    // search toggle
    $(document).on('click', 'button.action.search', function () {
        $('.field.search').slideToggle();
    });

    // secure checkout text
    $('.firecheckout .header .checkout-lock').append(
        document.createTextNode('Secure Checkout')
    );

    // responsive handler
    $(window).on('resize orientationchange', function () {
        responsive();
    });

    require(['jquery'], function ($) {
        $(document).ready(function () {
            $('body').trigger('processStop');
        });
    });

});