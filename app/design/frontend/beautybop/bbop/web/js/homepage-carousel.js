define([
    'jquery',
    'swiper'
], function ($, Swiper) {
    'use strict';

    if (!$('.bb-home-carousel').length) {
        return;
    }

    new Swiper('.bb-home-carousel', {

        loop: true,

        speed: 600,

        spaceBetween: 24,

        autoplay: {
            delay: 5000,
            disableOnInteraction: false
        },

        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },

        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },

        breakpoints: {

            320: {
                slidesPerView: 1.2,
                spaceBetween: 16
            },

            640: {
                slidesPerView: 2,
                spaceBetween: 20
            },

            768: {
                slidesPerView: 3,
                spaceBetween: 24
            },

            1280: {
                slidesPerView: 4,
                spaceBetween: 24
            }

        }

    });

});