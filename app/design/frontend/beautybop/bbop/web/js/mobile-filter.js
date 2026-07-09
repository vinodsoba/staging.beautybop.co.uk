define([
    'jquery'
], function ($) {

    'use strict';

    console.log('✅ Mobile Filter Loaded');

    $(function () {

        if ($(window).width() > 768) {
            return;
        }

        // Don't add twice
        if ($('.bb-mobile-toolbar').length) {
            return;
        }

        $('.toolbar-products').first().prepend(`
            <div class="bb-mobile-toolbar">

                <button class="bb-filter-btn">

                    <span>☰</span>

                    Filters

                </button>

                <button class="bb-sort-btn">

                    <span>⇅</span>

                    Sort

                </button>

            </div>
        `);


        if (!$('.bb-filter-header').length) {

            $('.block.filter').prepend(`
                <div class="bb-filter-header">

                    <button class="bb-filter-close">
                        ✕
                    </button>

                    <h2>Filters</h2>

                </div>
            `);

        }

        if (!$('.bb-filter-overlay').length) {

            $('body').append('<div class="bb-filter-overlay"></div>');

        }


        function openDrawer() {

            $('.sidebar-main').addClass('is-open');

            $('.bb-filter-overlay').addClass('active');

            $('body').addClass('bb-no-scroll');

        }


        function closeDrawer() {

            $('.sidebar-main').removeClass('is-open');

            $('.bb-filter-overlay').removeClass('active');

            $('body').removeClass('bb-no-scroll');

        }

    
        // click to open

       $(document).on('click', '.bb-filter-btn', function () {

           openDrawer();

            

        });

        // click to close

        $(document).on('click', '.bb-filter-close', function () {

             closeDrawer();


        });


        $(document).on('click', '.bb-filter-overlay', function () {

            closeDrawer();

        });


        $(document).on('click', '.filter-options-title', function () {

            $('.filter-options-title')
                .not(this)
                .removeClass('active');

            $(this).toggleClass('active');

        });

    });

});