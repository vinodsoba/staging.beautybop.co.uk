var config = {
    paths: {
        swiper: 'js/swiper',
        mobileMenu: 'js/mobile-menu'
    },

    shim: {
        swiper: {
            deps: ['jquery'],
            init: function () {
                return window.Swiper;
            }
        }
    }
};