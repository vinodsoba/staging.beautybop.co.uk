define([], function () {
    'use strict';

    return function (Component) {

        return Component.extend({

            /**
             * Return Cart Items component
             */
            getCartItems: function () {

                console.log('Cart Items:', this.elems());

                return this.elems().filter(function (item) {
                    return item.index === 'cart_items';
                });

            },

            /**
             * Return Totals component
             */
            getTotals: function () {

                return this.elems().filter(function (item) {
                    return item.index === 'totals';
                });

            },

            /**
             * Return itemsBefore
             */
            getItemsBefore: function () {

                return this.elems().filter(function (item) {
                    return item.index === 'itemsBefore';
                });

            },

            /**
             * Return itemsAfter
             */
            getItemsAfter: function () {

                return this.elems().filter(function (item) {
                    return item.index === 'itemsAfter';
                });

            }

        });

    };

});