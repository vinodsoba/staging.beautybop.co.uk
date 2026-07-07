define([
    'Magento_Checkout/js/model/totals'
], function (totals) {
    'use strict';

    console.log('✅ BeautyBop abstract-total mixin loaded');

    return function (Component) {

        return Component.extend({

            isFullMode: function () {

                console.log('BeautyBop isFullMode()', totals.totals());

                return !!totals.totals();

            }

        });

    };

});