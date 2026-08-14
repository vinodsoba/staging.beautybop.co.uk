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


     if (!$('.bb-home-carousel-slider-show').length) {
        return;
    }

      new Swiper('.bb-home-carousel-slider-show', {

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
                slidesPerView: 1,
                spaceBetween: 16
            },

            640: {
                slidesPerView: 1,
                spaceBetween: 20
            },

            768: {
                slidesPerView: 1,
                spaceBetween: 24
            },

            1280: {
                slidesPerView: 1,
                spaceBetween: 24
            }

        }

    });


    // brands slider
    if (!$('.bb-home-brands-slider-show').length) {
        return;
    }

    new Swiper('.bb-home-brands-slider-show', {

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
                slidesPerView: 1,
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

    /*new Swiper('.bb-trust-mobile', {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: true,

    autoplay: {
        delay: 4000,
        disableOnInteraction: false
    }
    
    });*/



});